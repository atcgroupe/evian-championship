<?php

namespace App\Enum;

enum UserRole: string implements AppEnumInterface
{
    case ADMIN = 'ROLE_ADMIN';
    case CUSTOMER = 'ROLE_CUSTOMER';
    case PROJECT_MANAGER = 'ROLE_PROJECT_MANAGER';
    case GRAPHIC_DESIGNER = 'ROLE_GRAPHIC_DESIGNER';
    case SHIPPING_MANAGER = 'ROLE_SHIPPING_MANAGER';

    public function getLabel(): string
    {
        return match($this)
        {
            UserRole::ADMIN => 'ADMIN',
            UserRole::CUSTOMER => 'CLIENT',
            UserRole::PROJECT_MANAGER => 'CHARGE DE PROJET',
            UserRole::GRAPHIC_DESIGNER => 'GRAPHISTE',
            UserRole::SHIPPING_MANAGER => 'RESPONSABLE EXPEDITIONS',
        };
    }

    use AppEnumTrait;
}
