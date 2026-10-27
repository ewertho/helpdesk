<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Priority;
use App\Enums\Status;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

#[Fillable(['subject', 'description', 'priority', 'category', 'attachment_path', 'attachment_name'])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $attributes = ['status' => 'open', 'priority' => 'normal'];

    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'priority' => Priority::class,
            'category' => Category::class,
            'due_at' => 'datetime',
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // O prazo nasce com o chamado e acompanha a prioridade.
        static::saving(function (Ticket $ticket) {
            // Fixa a data de criação antes de derivar o prazo: os dois partem do mesmo instante.
            $ticket->created_at ??= $ticket->freshTimestamp();
            if (! $ticket->exists || $ticket->isDirty('priority')) {
                $ticket->due_at = $ticket->created_at->copy()->addHours($ticket->priority->slaHours());
            }
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest('id');
    }

    /** Cliente só enxerga os próprios chamados; a equipe enxerga todos. */
    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->unless($user->isStaff(), fn ($q) => $q->where('requester_id', $user->id));
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', Status::active());
    }

    public function scopeBreached(Builder $query): void
    {
        $query->whereIn('status', Status::active())->where('due_at', '<', now());
    }

    /** met | breached | at_risk | ok */
    public function slaState(): string
    {
        if ($this->resolved_at) {
            return $this->resolved_at <= $this->due_at ? 'met' : 'breached';
        }
        if (now() > $this->due_at) {
            return 'breached';
        }

        // Em risco quando resta menos de um quarto do prazo.
        return now()->diffInMinutes($this->due_at) < $this->priority->slaHours() * 15 ? 'at_risk' : 'ok';
    }

    public function slaLabel(): string
    {
        return match (true) {
            (bool) $this->resolved_at => $this->slaState() === 'met' ? 'Resolvido no prazo' : 'Resolvido fora do prazo',
            $this->slaState() === 'breached' => 'Venceu '.$this->due_at->diffForHumans(),
            default => 'Vence '.$this->due_at->diffForHumans(),
        };
    }

    public function number(): string
    {
        return '#'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function changeStatus(Status $to, User $by): void
    {
        if ($this->status === $to) {
            return;
        }
        if (! $this->status->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => "Um chamado {$this->status->label()} não pode ir para {$to->label()}.",
            ]);
        }

        $from = $this->status;
        $this->status = $to;
        // Reabrir limpa a data de solução; resolver e depois encerrar preserva a original.
        $this->resolved_at = $to->isDone() ? ($this->resolved_at ?? now()) : null;
        $this->save();
        $this->log($by, "mudou o status de {$from->label()} para {$to->label()}");
    }

    public function assignTo(?User $assignee, User $by): void
    {
        if ($this->assignee_id === $assignee?->id) {
            return;
        }
        if ($assignee && ! $assignee->isStaff()) {
            throw ValidationException::withMessages(['assignee_id' => 'Só a equipe pode ser responsável por um chamado.']);
        }

        $this->assignee()->associate($assignee)->save();
        $this->log($by, $assignee ? "atribuiu o chamado a {$assignee->name}" : 'removeu o responsável');
    }

    public function changePriority(Priority $to, User $by): void
    {
        if ($this->priority === $to) {
            return;
        }
        $from = $this->priority;
        $this->priority = $to;
        $this->save();
        $this->log($by, "mudou a prioridade de {$from->label()} para {$to->label()}");
    }

    /** Responder move o chamado: a regra está aqui, não espalhada pelos controllers. */
    public function reply(User $by, string $body, bool $internal = false): Comment
    {
        return DB::transaction(function () use ($by, $body, $internal) {
            $comment = $this->comments()->create(['user_id' => $by->id, 'body' => $body, 'internal' => $internal]);

            if ($internal) {
                return $comment;
            }

            if ($by->isStaff()) {
                $this->first_response_at ??= now();
                $this->save();
                if (! $this->assignee_id) {
                    $this->assignTo($by, $by);
                }
                if ($this->status === Status::Open) {
                    $this->changeStatus(Status::InProgress, $by);
                }
            } elseif (in_array($this->status, [Status::WaitingCustomer, Status::Resolved], true)) {
                // Cliente respondeu: a bola volta para a equipe (e reabre se já estava resolvido).
                $this->changeStatus(Status::InProgress, $by);
            }

            return $comment;
        });
    }

    /** Avaliação do atendimento (CSAT): uma nota por chamado, dada por quem abriu, depois de resolvido. */
    public function rate(int $rating, ?string $comment, User $by): void
    {
        $this->rating = $rating;
        $this->rating_comment = $comment;
        $this->save();
        $this->log($by, "avaliou o atendimento com nota {$rating} de 5");
    }

    /** Fração do prazo já consumida, de 0 a 1, para a barra de SLA. */
    public function slaProgress(): float
    {
        $total = $this->created_at->diffInSeconds($this->due_at);
        $spent = $this->created_at->diffInSeconds($this->resolved_at ?? now());

        return $total > 0 ? min(1, max(0, $spent / $total)) : 1;
    }

    private function log(User $by, string $event): void
    {
        $this->comments()->create(['user_id' => $by->id, 'event' => $event]);
    }
}
