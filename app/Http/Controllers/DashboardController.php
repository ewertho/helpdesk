<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\Status;
use App\Models\Ticket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $resolved = Ticket::whereNotNull('resolved_at');
        $resolvedCount = (clone $resolved)->count();

        // ponytail: média calculada em PHP sobre os chamados respondidos. Com dezenas de milhares
        // de chamados, gravar os minutos até a primeira resposta numa coluna e usar AVG no banco.
        $firstResponse = Ticket::whereNotNull('first_response_at')->get(['id', 'created_at', 'first_response_at'])
            ->avg(fn (Ticket $ticket) => $ticket->created_at->diffInMinutes($ticket->first_response_at));

        $byStatus = Ticket::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $byCategory = Ticket::active()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category');

        return view('dashboard', [
            'active' => Ticket::active()->count(),
            'breached' => Ticket::breached()->count(),
            'unassigned' => Ticket::active()->whereNull('assignee_id')->count(),
            'resolvedWeek' => (clone $resolved)->where('resolved_at', '>=', now()->subDays(7))->count(),
            'slaRate' => $resolvedCount ? (int) round((clone $resolved)->whereColumn('resolved_at', '<=', 'due_at')->count() / $resolvedCount * 100) : null,
            'firstResponse' => $firstResponse,
            'csat' => Ticket::whereNotNull('rating')->avg('rating'),
            'csatCount' => Ticket::whereNotNull('rating')->count(),
            'byStatus' => collect(Status::cases())->map(fn (Status $status) => [$status, $byStatus[$status->value] ?? 0]),
            'byCategory' => collect(Category::cases())->map(fn (Category $category) => [$category, $byCategory[$category->value] ?? 0]),
            'workload' => Ticket::active()->whereNotNull('assignee_id')->with('assignee')->get()->groupBy('assignee_id')
                ->map(fn ($tickets) => [$tickets->first()->assignee, $tickets->count()])->sortByDesc(fn ($row) => $row[1])->values(),
            'next' => Ticket::active()->with('requester', 'assignee')->oldest('due_at')->limit(5)->get(),
        ]);
    }
}
