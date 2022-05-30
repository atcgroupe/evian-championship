<?php

namespace App\Enum;

enum JobStatus: int implements AppEnumInterface
{
    case CREATED = 1;
    case SENT = 2;
    case APPROVAL = 3;
    case APPROVED = 4;
    case REJECTED = 5;
    case PRODUCTION = 6;
    case SHIPPED = 7;
    case STANDBY = 8;
    case CANCELED = 9;

    use AppEnumTrait;

    public function getLabel(): string
    {
        return match($this)
        {
            JobStatus::CREATED => 'Brouillon',
            JobStatus::SENT => 'A traiter par ATC',
            JobStatus::APPROVAL => 'En attente de validation',
            JobStatus::APPROVED => 'Bat validé',
            JobStatus::REJECTED => 'Bat rejeté',
            JobStatus::PRODUCTION => 'En production',
            JobStatus::SHIPPED => 'Expédié',
            JobStatus::STANDBY => 'En stand-by',
            JobStatus::CANCELED => 'Annulé',
        };
    }
}
