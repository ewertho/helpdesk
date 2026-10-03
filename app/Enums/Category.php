<?php

namespace App\Enums;

enum Category: string
{
    case Access = 'access';
    case Hardware = 'hardware';
    case Software = 'software';
    case Network = 'network';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Access => 'Acesso e senhas',
            self::Hardware => 'Equipamento',
            self::Software => 'Sistemas',
            self::Network => 'Rede e internet',
            self::Other => 'Outros',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Access => 'key',
            self::Hardware => 'monitor',
            self::Software => 'window',
            self::Network => 'wifi',
            self::Other => 'help',
        };
    }
}
