<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

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

        $open = fn (User $by, string $subject, string $description, Category $category, Priority $priority, int $hoursAgo) => Ticket::forceCreate([
            'requester_id' => $by->id,
            'subject' => $subject,
            'description' => $description,
            'category' => $category,
            'priority' => $priority,
            'created_at' => now()->subHours($hoursAgo),
        ]);

        $open($customer, 'Não consigo acessar o e-mail corporativo', 'Desde hoje cedo o webmail recusa minha senha. Já tentei no celular e no notebook.', Category::Access, Priority::High, 2);

        $open($other, 'Sistema de pedidos fora do ar', 'A tela de pedidos mostra erro 500 para toda a equipe de vendas.', Category::Software, Priority::Urgent, 6);

        $printer = $open($customer, 'Impressora do 2º andar não imprime', 'Os trabalhos ficam na fila e nada sai. A luz laranja está piscando.', Category::Hardware, Priority::Normal, 20);
        $printer->reply($agent, 'Pode me dizer qual mensagem aparece no painel da impressora?');
        $printer->changeStatus(Status::WaitingCustomer, $agent);

        $vpn = $open($other, 'VPN cai a cada 10 minutos', 'Trabalhando de casa, a VPN desconecta sozinha várias vezes por hora.', Category::Network, Priority::Normal, 30);
        $vpn->reply($agent, 'Estou verificando os logs do concentrador.');
        $vpn->reply($agent, 'Parece ligado à troca de certificado da semana passada.', internal: true);

        $notebook = $open($customer, 'Solicitação de notebook para novo colaborador', 'Novo analista começa na segunda. Precisa de notebook com acesso ao ERP.', Category::Hardware, Priority::Low, 50);
        $notebook->assignTo($admin, $admin);
        $notebook->reply($admin, 'Notebook separado e configurado. Pode retirar no TI.');
        $notebook->changeStatus(Status::Resolved, $admin);
    }
}
