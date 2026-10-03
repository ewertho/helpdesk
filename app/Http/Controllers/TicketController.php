<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Role;
use App\Enums\Status;
use App\Models\CannedReply;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    /** Cada fila é um recorte nomeado; a navegação mostra a contagem de cada uma. */
    private function queues(User $user): array
    {
        $base = fn () => Ticket::visibleTo($user);

        $queues = ['active' => ['Em andamento', $base()->active()]];
        if ($user->isStaff()) {
            $queues += [
                'mine' => ['Comigo', $base()->active()->where('assignee_id', $user->id)],
                'unassigned' => ['Sem responsável', $base()->active()->whereNull('assignee_id')],
                'breached' => ['Fora do prazo', $base()->breached()],
            ];
        }

        return $queues + [
            'done' => ['Resolvidos', $base()->whereIn('status', [Status::Resolved, Status::Closed])],
            'all' => ['Todos', $base()],
        ];
    }

    public function index(Request $request): View
    {
        $queues = $this->queues($request->user());
        $current = array_key_exists($request->query('fila'), $queues) ? $request->query('fila') : 'active';
        $search = trim((string) $request->query('q'));

        $tickets = $queues[$current][1]
            ->with('requester', 'assignee')
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('subject', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('id', ltrim($search, '#0'))))
            // Fila ativa por prazo (o que vence primeiro no topo); o resto, do mais recente.
            ->when(in_array($current, ['done', 'all']), fn ($q) => $q->latest(), fn ($q) => $q->oldest('due_at'))
            ->paginate(20)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'current' => $current,
            'search' => $search,
            'queues' => array_map(fn ($queue) => ['label' => $queue[0], 'count' => $queue[1]->count()], $queues),
        ]);
    }

    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'min:5', 'max:160'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'category' => ['required', Rule::enum(Category::class)],
            'priority' => ['required', Rule::enum(Priority::class)],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:png,jpg,jpeg,webp,pdf,txt,log'],
        ]);

        if ($file = $request->file('attachment')) {
            // Disco privado e nome gerado: o arquivo só sai pela rota autorizada abaixo.
            $data['attachment_path'] = $file->store('attachments');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        unset($data['attachment']);

        $ticket = new Ticket($data);
        $ticket->requester()->associate($request->user())->save();

        return redirect()->route('tickets.show', $ticket)->with('status', "Chamado #{$ticket->id} aberto.");
    }

    public function show(Request $request, Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);
        $staff = $request->user()->isStaff();

        return view('tickets.show', [
            'ticket' => $ticket->load('requester', 'assignee'),
            // Nota interna nunca chega à tela do cliente.
            'comments' => $ticket->comments()->with('user')->when(! $staff, fn ($q) => $q->where('internal', false))->get(),
            'canned' => $staff ? CannedReply::orderBy('title')->get() : collect(),
            'agents' => $staff ? User::whereIn('role', [Role::Agent, Role::Admin])->orderBy('name')->get() : collect(),
        ]);
    }

    /** Status, prioridade e responsável: cada campo enviado passa pela sua própria regra. */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('view', $ticket);
        $user = $request->user();

        $data = $request->validate([
            'status' => ['sometimes', Rule::enum(Status::class)],
            'priority' => ['sometimes', Rule::enum(Priority::class)],
            'assignee_id' => ['sometimes', 'nullable', 'exists:users,id'],
        ]);

        if (isset($data['status'])) {
            $status = Status::from($data['status']);
            Gate::authorize('changeStatus', [$ticket, $status]);
            $ticket->changeStatus($status, $user);
        }
        if (isset($data['priority'])) {
            Gate::authorize('staff');
            $ticket->changePriority(Priority::from($data['priority']), $user);
        }
        if (array_key_exists('assignee_id', $data)) {
            Gate::authorize('staff');
            $ticket->assignTo($data['assignee_id'] ? User::find($data['assignee_id']) : null, $user);
        }

        return back()->with('status', 'Chamado atualizado.');
    }

    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $internal = $request->boolean('internal') && $request->user()->isStaff();

        $ticket->reply($request->user(), $data['body'], $internal);

        return back()->with('status', $internal ? 'Nota interna adicionada.' : 'Resposta enviada.');
    }

    public function rate(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('rate', $ticket);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'rating_comment' => ['nullable', 'string', 'max:500'],
        ]);
        $ticket->rate((int) $data['rating'], $data['rating_comment'] ?? null, $request->user());

        return back()->with('status', 'Obrigado pela avaliação.');
    }

    public function attachment(Ticket $ticket): StreamedResponse
    {
        Gate::authorize('view', $ticket);
        abort_unless($ticket->attachment_path, 404);

        return Storage::download($ticket->attachment_path, $ticket->attachment_name);
    }
}
