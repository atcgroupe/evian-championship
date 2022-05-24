<?php

namespace App\Repository;

use App\Entity\Job;
use App\Enum\JobStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 *
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Job $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Job $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Job[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.delivery', 'delivery')
                ->addSelect('delivery')
            ->leftJoin('j.product', 'product')
                ->addSelect('product')
            ->leftJoin('j.jobFiles', 'files')
                ->addSelect('files')
            ->leftJoin('j.validationFiles', 'validationFiles')
                ->addSelect('validationFiles')
            ->orderBy('j.customerReference', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     * @return Job|null
     */
    public function findWithRelations(int $id): Job | null
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.id = :id')
                ->setParameter('id', $id)
            ->leftJoin('j.delivery', 'delivery')
                ->addSelect('delivery')
            ->leftJoin('j.product', 'product')
                ->addSelect('product')
            ->leftJoin('j.jobFiles', 'files')
                ->addSelect('files')
            ->leftJoin('j.validationFiles', 'validationFiles')
                ->addSelect('validationFiles')
            ->leftJoin('j.jobLogs', 'logs')
                ->addSelect('logs')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Job[]|null
     */
    public function findSentWidthRelations(): array | null
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.status != :status')
                ->setParameter('status', JobStatus::CREATED->getValue())
            ->leftJoin('j.delivery', 'delivery')
                ->addSelect('delivery')
            ->leftJoin('j.product', 'product')
                ->addSelect('product')
            ->leftJoin('j.jobFiles', 'files')
                ->addSelect('files')
            ->leftJoin('j.validationFiles', 'validationFiles')
                ->addSelect('validationFiles')
            ->leftJoin('j.jobLogs', 'logs')
                ->addSelect('logs')
            ->getQuery()
            ->getResult();
    }
}
