<?php

namespace App\Enums;

enum Status: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingCustomer = 'waiting_customer';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Aberto',
            self::InProgress => 'Em atendimento',
            self::WaitingCustomer => 'Aguardando cliente',
            self::Resolved => 'Resolvido',
            self::Closed => 'Encerrado',
        };
    }

    /**
     * O fluxo do chamado mora aqui: qualquer outra mudança é recusada.
     *
     * @return list<self>
     */
    public function next(): array
    {
        return match ($this) {
            self::Open => [self::InProgress, self::WaitingCustomer, self::Resolved],
            self::InProgress => [self::WaitingCustomer, self::Resolved],
            self::WaitingCustomer => [self::InProgress, self::Resolved],
            self::Resolved => [self::InProgress, self::Closed],
            self::Closed => [],
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return in_array($to, $this->next(), true);
    }

    public function isDone(): bool
    {
        return $this === self::Resolved || $this === self::Closed;
    }

    /** @return list<self> */
    public static function active(): array
    {
        return [self::Open, self::InProgress, self::WaitingCustomer];
    }
}
