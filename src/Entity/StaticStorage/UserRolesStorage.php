<?php

namespace App\Entity\StaticStorage;

use JetBrains\PhpStorm\ArrayShape;

class UserRolesStorage
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_SUPER_ADMIN,
    ];

    #[ArrayShape([
        self::ROLE_USER => "string",
        self::ROLE_ADMIN => "string",
        self::ROLE_SUPER_ADMIN => "string"
    ])]
    public static function getRoleChoices(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_SUPER_ADMIN => 'Super Admin',
        ];
    }
}
