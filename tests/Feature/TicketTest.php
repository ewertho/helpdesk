<?php

namespace Tests\Feature;

use App\Enums\Priority;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $agent;

    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create();
        $this->agent = User::factory()->role(Role::Agent)->create();
        $this->ticket = Ticket::factory()->create(['requester_id' => $this->customer->id]);
    }

    public function test_prazo_nasce_da_prioridade_e_acompanha_a_mudanca(): void
    {
        $this->assertEquals($this->ticket->created_at->addHours(24), $this->ticket->due_at);

        $this->ticket->changePriority(Priority::Urgent, $this->agent);

        $this->assertEquals($this->ticket->created_at->addHours(4), $this->ticket->fresh()->due_at);
    }

    public function test_sla_fica_em_risco_estoura_e_registra_se_foi_cumprido(): void
    {
        $this->assertSame('ok', $this->ticket->slaState());

        $this->travel(19)->hours();
        $this->assertSame('at_risk', $this->ticket->slaState());

        $this->travel(6)->hours();
        $this->assertSame('breached', $this->ticket->slaState());
        $this->assertSame(1, Ticket::breached()->count());

        $this->ticket->changeStatus(Status::Resolved, $this->agent);
        $this->assertSame('breached', $this->ticket->slaState());
        $this->assertSame(0, Ticket::breached()->count());
    }

    public function test_fluxo_de_status_recusa_atalhos(): void
    {
        $this->expectException(ValidationException::class);

        $this->ticket->changeStatus(Status::Closed, $this->agent);
    }

    public function test_resposta_da_equipe_assume_o_chamado_e_marca_a_primeira_resposta(): void
    {
        $this->ticket->reply($this->agent, 'Vou verificar.');

        $this->assertSame(Status::InProgress, $this->ticket->status);
        $this->assertSame($this->agent->id, $this->ticket->assignee_id);
        $this->assertNotNull($this->ticket->first_response_at);
    }

    public function test_nota_interna_nao_move_o_chamado_nem_aparece_para_o_cliente(): void
    {
        $this->ticket->reply($this->agent, 'segredo da equipe', internal: true);

        $this->assertSame(Status::Open, $this->ticket->status);
        $this->assertNull($this->ticket->first_response_at);
        $this->actingAs($this->customer)->get(route('tickets.show', $this->ticket))->assertOk()->assertDontSee('segredo da equipe');
        $this->actingAs($this->agent)->get(route('tickets.show', $this->ticket))->assertSee('segredo da equipe');
    }

    public function test_cliente_nao_consegue_criar_nota_interna(): void
    {
        $this->actingAs($this->customer)->post(route('tickets.reply', $this->ticket), ['body' => 'oi', 'internal' => '1']);

        $this->assertFalse($this->ticket->comments()->first()->internal);
    }

    public function test_resposta_do_cliente_reabre_chamado_resolvido(): void
    {
        $this->ticket->changeStatus(Status::Resolved, $this->agent);
        $this->assertNotNull($this->ticket->resolved_at);

        $this->ticket->reply($this->customer, 'Voltou a acontecer.');

        $this->assertSame(Status::InProgress, $this->ticket->status);
        $this->assertNull($this->ticket->resolved_at);
    }

    public function test_chamado_encerrado_nao_aceita_resposta(): void
    {
        $this->ticket->changeStatus(Status::Resolved, $this->agent);
        $this->ticket->changeStatus(Status::Closed, $this->agent);

        $this->actingAs($this->customer)->post(route('tickets.reply', $this->ticket), ['body' => 'oi'])->assertForbidden();
    }

    public function test_cliente_so_ve_os_proprios_chamados(): void
    {
        $alheio = Ticket::factory()->create(['subject' => 'Chamado de outra pessoa']);

        $this->actingAs($this->customer)->get(route('tickets.show', $alheio))->assertForbidden();
        $this->actingAs($this->customer)->get(route('tickets.index', ['fila' => 'all']))
            ->assertSee($this->ticket->subject)->assertDontSee('Chamado de outra pessoa');
        $this->actingAs($this->agent)->get(route('tickets.index', ['fila' => 'all']))->assertSee('Chamado de outra pessoa');
    }

    public function test_cliente_pode_resolver_o_proprio_chamado_mas_nao_gerenciar(): void
    {
        $update = fn (array $data) => $this->actingAs($this->customer)->patch(route('tickets.update', $this->ticket), $data);

        $update(['priority' => 'urgent'])->assertForbidden();
        $update(['assignee_id' => $this->agent->id])->assertForbidden();
        $update(['status' => 'in_progress'])->assertForbidden();
        $update(['status' => 'resolved'])->assertRedirect();

        $this->assertSame(Status::Resolved, $this->ticket->fresh()->status);
        $this->assertSame(Priority::Normal, $this->ticket->fresh()->priority);
    }

    public function test_cliente_nao_pode_ser_responsavel(): void
    {
        $this->actingAs($this->agent)->patch(route('tickets.update', $this->ticket), ['assignee_id' => $this->customer->id])
            ->assertSessionHasErrors('assignee_id');
    }

    public function test_abrir_chamado_guarda_anexo_em_disco_privado_e_so_libera_para_quem_pode_ver(): void
    {
        Storage::fake();

        $this->actingAs($this->customer)->post(route('tickets.store'), [
            'subject' => 'Tela azul ao ligar',
            'description' => 'O notebook mostra tela azul logo depois do logo.',
            'category' => 'hardware',
            'priority' => 'high',
            'attachment' => UploadedFile::fake()->create('tela.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $ticket = Ticket::latest('id')->first();
        Storage::assertExists($ticket->attachment_path);
        $this->assertSame($this->customer->id, $ticket->requester_id);
        $this->assertSame(Status::Open, $ticket->status);

        $this->actingAs($this->customer)->get(route('tickets.attachment', $ticket))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('tickets.attachment', $ticket))->assertForbidden();
    }

    public function test_anexo_executavel_e_recusado(): void
    {
        $this->actingAs($this->customer)->post(route('tickets.store'), [
            'subject' => 'Assunto válido',
            'description' => 'Descrição com tamanho suficiente.',
            'category' => 'other',
            'priority' => 'low',
            'attachment' => UploadedFile::fake()->create('shell.php', 10),
        ])->assertSessionHasErrors('attachment');
    }

    public function test_cadastro_ignora_tentativa_de_escolher_o_papel(): void
    {
        $this->post('/register', [
            'name' => 'Esperto',
            'email' => 'esperto@example.com',
            'password' => 'senha-segura-1',
            'password_confirmation' => 'senha-segura-1',
            'role' => 'admin',
        ])->assertRedirect(route('tickets.index'));

        $this->assertSame(Role::Customer, User::firstWhere('email', 'esperto@example.com')->role);
    }

    public function test_so_admin_gerencia_papeis_e_nao_pode_se_rebaixar(): void
    {
        $admin = User::factory()->role(Role::Admin)->create();

        $this->actingAs($this->agent)->get(route('users.index'))->assertForbidden();
        $this->actingAs($admin)->patch(route('users.update', $admin), ['role' => 'customer'])->assertSessionHasErrors('role');
        $this->actingAs($admin)->patch(route('users.update', $this->customer), ['role' => 'agent'])->assertSessionHasNoErrors();

        $this->assertSame(Role::Admin, $admin->fresh()->role);
        $this->assertSame(Role::Agent, $this->customer->fresh()->role);
    }

    public function test_so_quem_abriu_avalia_uma_vez_e_depois_de_resolvido(): void
    {
        $rate = fn (User $as, array $data = ['rating' => 5]) => $this->actingAs($as)->post(route('tickets.rate', $this->ticket), $data);

        $rate($this->customer)->assertForbidden(); // ainda em aberto
        $this->ticket->changeStatus(Status::Resolved, $this->agent);

        $rate($this->agent)->assertForbidden();
        $rate($this->customer, ['rating' => 9])->assertSessionHasErrors('rating');
        $rate($this->customer, ['rating' => 4, 'rating_comment' => 'Resolveu rápido'])->assertSessionHasNoErrors();
        $rate($this->customer)->assertForbidden(); // já avaliou

        $this->assertSame(4, $this->ticket->fresh()->rating);
    }

    public function test_painel_e_respostas_prontas_sao_so_da_equipe(): void
    {
        $this->actingAs($this->customer)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($this->customer)->post(route('canned.store'), ['title' => 'x', 'body' => 'y'])->assertForbidden();

        $this->actingAs($this->agent)->post(route('canned.store'), ['title' => 'Saudação', 'body' => 'Olá, tudo bem?'])->assertSessionHasNoErrors();
        $this->actingAs($this->agent)->get(route('tickets.show', $this->ticket))->assertSee('Saudação');
        $this->actingAs($this->customer)->get(route('tickets.show', $this->ticket))->assertDontSee('Saudação');
    }

    public function test_painel_calcula_prazo_cumprido_primeira_resposta_e_satisfacao(): void
    {
        $this->travel(30)->minutes();
        $this->ticket->reply($this->agent, 'Vou verificar.');
        $this->ticket->changeStatus(Status::Resolved, $this->agent);
        $this->ticket->rate(4, null, $this->customer);

        $atrasado = Ticket::factory()->create();
        $this->travel(30)->hours();
        $atrasado->changeStatus(Status::Resolved, $this->agent);

        $this->actingAs($this->agent)->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('slaRate', 50)
            ->assertViewHas('firstResponse', 30.0)
            ->assertViewHas('csat', 4.0)
            ->assertViewHas('resolvedWeek', 2);
    }

    public function test_visitante_e_mandado_para_o_login(): void
    {
        $this->get(route('tickets.index'))->assertRedirect(route('login'));
    }
}
