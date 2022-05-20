<?php

namespace App\Repository;

use App\Entity\UserEventNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEventNotification>
 *
 * @method UserEventNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEventNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEventNotification[]    findAll()
 * @method UserEventNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEventNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEventNotification::class);
    }

    public function add(UserEventNotification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserEventNotification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
