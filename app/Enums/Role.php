<?php

namespace App\Enums;

enum Role: string
{
    case Customer = 'customer';
    case Agent = 'agent';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Cliente',
            self::Agent => 'Atendente',
            self::Admin => 'Administrador',
        };
    }
}
