<?php

namespace App\Enums;

enum Priority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Baixa',
            self::Normal => 'Normal',
            self::High => 'Alta',
            self::Urgent => 'Urgente',
        };
    }

    /** Prazo de solução. ponytail: horas corridas; trocar por calendário comercial se o SLA exigir. */
    public function slaHours(): int
    {
        return match ($this) {
            self::Low => 72,
            self::Normal => 24,
            self::High => 8,
            self::Urgent => 4,
        };
    }
}
