<?php

namespace App\Enum;

enum JobEvent: int implements AppEnumInterface
{
    case CREATED = 0; // A job has been created
    case SENT = 1; // A job has been sent to Company [ATC]
    case PAO = 13; // A graphic designer is handling the job [ATC + CUSTOMER]
    case APPROVAL = 2; // A job has been sent to the Customer for approval [CUSTOMER]
    case APPROVED = 3; // The job has been approved [ATC]
    case REJECTED = 4; // The job has been rejected [ATC]
    case UPDATE = 5; // The customer want to update the job after approval. [ATC]
    case PRODUCTION = 6; // The job has been sent to production [CUSTOMER]
    case SHIPPED = 7; // The job has been shipped [CUSTOMER]
    case REQUEST_UPDATE = 8; // The customer asks to modify the job if it is possible [ATC]
    case REQUEST_CANCEL = 9; // The job asks to cancel the job if it is possible [ATC]
    case CANCELED = 10; // The job has been canceled [ATC + CUSTOMER]
    case SENT_FOR_UPDATE = 11; // The job has been sent to customer for update [CUSTOMER]
    case DELETED = 12;

    case INFO_UPDATED = 102;
    case JOB_FILE_ADDED = 103;
    case JOB_FILE_REMOVED = 104;
    case VALIDATION_FILE_ADDED = 105;
    case VALIDATION_FILE_REMOVED = 106;
    case DELIVERY_INFO_UPDATED = 107;
    case STATUS_UPDATED = 108;

    use AppEnumTrait;

    public function getEvent(): string
    {
        return match($this)
        {
            JobEvent::CREATED => 'job.created',
            JobEvent::SENT => 'job.sent',
            JobEvent::PAO => 'job.pao',
            JobEvent::APPROVAL => 'job.sent.for.approval',
            JobEvent::APPROVED => 'job.approved',
            JobEvent::REJECTED => 'job.rejected',
            JobEvent::UPDATE => 'job.update.after.approval',
            JobEvent::PRODUCTION => 'job.sent.to.production',
            JobEvent::SHIPPED => 'job.shipped',
            JobEvent::REQUEST_UPDATE => 'job.request.for.update',
            JobEvent::REQUEST_CANCEL => 'job.request.for.cancel',
            JobEvent::CANCELED => "job.canceled",
            JobEvent::SENT_FOR_UPDATE => 'job.sent.to.customer.for.update',
            JobEvent::DELETED => 'job.deleted',
            JobEvent::INFO_UPDATED => 'job.info.updated',
            JobEvent::JOB_FILE_ADDED => 'job.jobfile.added',
            JobEvent::JOB_FILE_REMOVED => 'job.jobfile.removed',
            JobEvent::VALIDATION_FILE_ADDED => 'job.validationfile.added',
            JobEvent::VALIDATION_FILE_REMOVED => 'job.validationfile.removed',
            JobEvent::DELIVERY_INFO_UPDATED => 'job.delivery.info.updated',
            JobEvent::STATUS_UPDATED => 'job.status.updated',
        };
    }

    public function getLabel(): string
    {
        return match($this)
        {
            JobEvent::SENT => "Lorsqu'un job a été envoyé à ATC pour traitement",
            JobEvent::PAO => "Lorsqu'un job a été pris en PAO par un graphiste",
            JobEvent::APPROVAL => "Lorsque le BAT d'un job est disponible pour validation",
            JobEvent::APPROVED => "Lorsqu'un BAT a été validé",
            JobEvent::REJECTED => "Lorsqu'un BAT a été refusé",
            JobEvent::UPDATE => "Lorsque le client souhaite modifier le job après lecture du BAT",
            JobEvent::PRODUCTION => "Lorsqu'un job a été envoyé en production",
            JobEvent::SHIPPED => "Lorsqu'un job a été expédié",
            JobEvent::REQUEST_UPDATE => "Lorsque le client a demandé à modifier le job",
            JobEvent::REQUEST_CANCEL => "Lorsque le client a demandé à annuler le job",
            JobEvent::CANCELED => "Lorsqu'un job a été annulé",
            JobEvent::SENT_FOR_UPDATE => "Lorsqu'un job vous a été renvoyé pour modification",
            JobEvent::DELETED => "Lorsqu'un job a été supprimé définitivement",
        };
    }

    public function getEmailMessage(): string
    {
        return match($this)
        {
            JobEvent::SENT => "Un nouveau job a été soumis par le client",
            JobEvent::PAO => "Le job a été pris en PAO",
            JobEvent::APPROVAL => "Le BAT de ce job est disponible pour validation",
            JobEvent::APPROVED => "Le BAT de ce job a été approuvé par le client",
            JobEvent::REJECTED => "Le BAT de ce job a été rejeté par le client",
            JobEvent::UPDATE => "Le client va modifier ce job",
            JobEvent::PRODUCTION => "Ce job a été envoyé en production",
            JobEvent::SHIPPED => "Ce job a été expédié",
            JobEvent::REQUEST_UPDATE => "Le client a demandé à modifier ce job",
            JobEvent::REQUEST_CANCEL => "Le client a demandé à annuler ce job",
            JobEvent::CANCELED => "Ce job a été annulé",
            JobEvent::SENT_FOR_UPDATE => "Ce job est de nouveau disponible pour être modifié",
            JobEvent::DELETED => "Ce job a été supprimé définitivement.",
        };
    }

    public function getGroup(): JobEventGroup
    {
        return match($this)
        {
            JobEvent::SENT,
            JobEvent::APPROVED,
            JobEvent::REJECTED,
            JobEvent::UPDATE,
            JobEvent::REQUEST_UPDATE,
            JobEvent::REQUEST_CANCEL,
                => JobEventGroup::COMPANY,
            JobEvent::APPROVAL,
            JobEvent::PRODUCTION,
            JobEvent::SHIPPED,
            JobEvent::SENT_FOR_UPDATE
                => JobEventGroup::CUSTOMER,
            JobEvent::PAO,
            JobEvent::CANCELED,
            JobEvent::DELETED
                => JobEventGroup::ALL,
            default
                => JobEventGroup::NONE
        };
    }
}
