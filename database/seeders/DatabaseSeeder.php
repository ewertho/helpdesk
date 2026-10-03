<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\CannedReply;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Dados de demonstração: contas com senha "password" e uma fila com um pouco de cada situação.
 * Não usa factories de propósito: o Faker é dependência de dev e este seeder roda em produção.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = fn (string $name, string $email, Role $role = Role::Customer) => User::forceCreate([
            'name' => $name, 'email' => $email, 'password' => 'password', 'role' => $role,
        ]);

        $admin = $user('Ana Admin', 'admin@helpdesk.test', Role::Admin);
        $agent = $user('Bruno Atendente', 'agente@helpdesk.test', Role::Agent);
        $customer = $user('Carla Cliente', 'cliente@helpdesk.test');
        $other = $user('Diego Souza', 'diego@helpdesk.test');

        // Cada passo acontece "há N horas": o relógio é movido para o histórico sair com datas plausíveis.
        $now = now();
        $ago = function (float $hours, callable $step) use ($now) {
            Carbon::setTestNow($now->copy()->subMinutes((int) round($hours * 60)));
            try {
                return $step();
            } finally {
                Carbon::setTestNow();
            }
        };
        $open = fn (float $hours, User $by, string $subject, string $description, Category $category, Priority $priority) => $ago($hours, fn () => Ticket::forceCreate([
            'requester_id' => $by->id, 'subject' => $subject, 'description' => $description, 'category' => $category, 'priority' => $priority,
        ]));

        $open(2, $customer, 'Não consigo acessar o e-mail corporativo', 'Desde hoje cedo o webmail recusa minha senha. Já tentei no celular e no notebook.', Category::Access, Priority::High);

        $open(6, $other, 'Sistema de pedidos fora do ar', 'A tela de pedidos mostra erro 500 para toda a equipe de vendas.', Category::Software, Priority::Urgent);

        $printer = $open(20, $customer, 'Impressora do 2º andar não imprime', 'Os trabalhos ficam na fila e nada sai. A luz laranja está piscando.', Category::Hardware, Priority::Normal);
        $ago(19.5, fn () => $printer->reply($agent, 'Pode me dizer qual mensagem aparece no painel da impressora?'));
        $ago(19.4, fn () => $printer->changeStatus(Status::WaitingCustomer, $agent));

        $vpn = $open(30, $other, 'VPN cai a cada 10 minutos', 'Trabalhando de casa, a VPN desconecta sozinha várias vezes por hora.', Category::Network, Priority::Normal);
        $ago(29.2, fn () => $vpn->reply($agent, 'Estou verificando os logs do concentrador.'));
        $ago(26, fn () => $vpn->reply($agent, 'Parece ligado à troca de certificado da semana passada.', internal: true));

        $notebook = $open(50, $customer, 'Solicitação de notebook para novo colaborador', 'Novo analista começa na segunda. Precisa de notebook com acesso ao ERP.', Category::Hardware, Priority::Low);
        $ago(49, fn () => $notebook->assignTo($admin, $admin));
        $ago(27, fn () => $notebook->reply($admin, 'Notebook separado e configurado. Pode retirar no TI.'));
        $ago(26.9, fn () => $notebook->changeStatus(Status::Resolved, $admin));
        $ago(25, fn () => $notebook->rate(5, 'Rápido e resolveu de primeira.', $customer));

        $senha = $open(28, $other, 'Redefinir senha do ERP', 'Esqueci a senha do ERP e o link de recuperação não chega.', Category::Access, Priority::High);
        $ago(27.7, fn () => $senha->reply($agent, 'Senha redefinida. Você recebe a provisória por e-mail em instantes.'));
        $ago(27.6, fn () => $senha->changeStatus(Status::Resolved, $agent));
        $ago(27, fn () => $senha->rate(4, null, $other));

        $monitor = $open(75, $customer, 'Monitor piscando na sala de reunião', 'O monitor da sala 3 apaga e acende durante as chamadas de vídeo.', Category::Hardware, Priority::Normal);
        $ago(73, fn () => $monitor->reply($agent, 'Trocamos o cabo HDMI. Pode testar na próxima reunião?'));
        $ago(40, fn () => $monitor->changeStatus(Status::Resolved, $agent)); // resolvido fora do prazo de 24h
        $ago(38, fn () => $monitor->rate(4, 'Demorou um pouco, mas resolveu.', $customer));

        foreach ([
            'Pedir mais detalhes' => "Olá! Para investigar, preciso de mais alguns detalhes:\n\n1. Desde quando acontece?\n2. Aparece alguma mensagem de erro? Se sim, qual?\n3. Acontece em outro computador ou só no seu?",
            'Reiniciar e testar' => 'Por favor, reinicie o equipamento e tente de novo. Se o problema continuar, me avise por aqui que sigo com a análise.',
            'Chamado resolvido' => 'Fizemos o ajuste e está tudo funcionando do nosso lado. Pode testar e confirmar? Se estiver tudo certo, é só marcar o chamado como resolvido.',
            'Encaminhado ao fornecedor' => 'Acionamos o fornecedor responsável. O prazo deles é de até 2 dias úteis; aviso por aqui assim que tiver retorno.',
        ] as $title => $body) {
            CannedReply::create(['title' => $title, 'body' => $body]);
        }
    }
}
