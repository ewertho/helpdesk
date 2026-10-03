# Helpdesk

Sistema de chamados com fila por prazo, papéis e histórico completo de cada atendimento.

## O que faz

- **Chamados** com categoria, urgência, anexo e número próprio.
- **SLA por prioridade** (urgente 4h, alta 8h, normal 24h, baixa 72h): o prazo nasce com o chamado, acompanha a mudança de prioridade e a fila mostra o que está em risco ou vencido.
- **Fluxo de status com regras**: aberto, em atendimento, aguardando cliente, resolvido, encerrado. Só as transições válidas existem, e a tela só oferece o próximo passo que o usuário pode dar.
- **Responder move o chamado**: a primeira resposta da equipe assume o chamado e marca o tempo de primeira resposta; a resposta do cliente devolve a bola para a equipe e reabre um chamado resolvido.
- **Notas internas** visíveis só para a equipe.
- **Papéis**: cliente vê os próprios chamados; atendente vê e trabalha todos; administrador também define papéis.
- **Histórico auditável**: cada mudança de status, prioridade e responsável vira uma linha na conversa.

## Stack

| | |
|---|---|
| Aplicação | Laravel 13, PHP 8.5, Blade |
| Servidor | FrankenPHP (Caddy embutido), um container |
| Banco | SQLite em volume |
| Interface | CSS escrito à mão, sem etapa de build de front-end |
| Testes | PHPUnit 12, 16 testes de regra de negócio e autorização |

## Decisões

- **As regras moram no domínio.** [`Ticket`](app/Models/Ticket.php) e [`Status`](app/Enums/Status.php) concentram fluxo, SLA e efeitos de uma resposta; os controllers só validam e autorizam.
- **Autorização em um lugar só**: [`TicketPolicy`](app/Policies/TicketPolicy.php) e um escopo `visibleTo` que toda consulta de chamados atravessa.
- **Anexos em disco privado**, com nome gerado e tipo restrito, servidos por rota autorizada. Nada de arquivo de usuário em pasta pública.
- **`role` fora do `fillable`**: ninguém se promove pelo formulário de cadastro. O administrador não pode alterar o próprio papel, para não trancar o painel.
- Login e cadastro com limite de tentativas; modo estrito do Eloquent em desenvolvimento (N+1 vira erro).

## Rodando

```bash
docker compose up -d --build
```

Sobe em `http://localhost:8102` já migrado e com dados de demonstração. Contas (senha `password`):

| Papel | E-mail |
|---|---|
| Cliente | cliente@helpdesk.test |
| Atendente | agente@helpdesk.test |
| Administrador | admin@helpdesk.test |

Para um ambiente real, defina `APP_DEMO=false`, `APP_URL` e, se quiser controlar a chave, `APP_KEY` (sem ela, uma é gerada e guardada no volume).

## Testes

```bash
docker run --rm -v "$PWD:/app" -w /app php:8.5-cli php artisan test
```
