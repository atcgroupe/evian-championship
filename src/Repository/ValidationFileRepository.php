<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\ValidationFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ValidationFile>
 *
 * @method ValidationFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValidationFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValidationFile[]    findAll()
 * @method ValidationFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValidationFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValidationFile::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ValidationFile $entity, bool $flush = false): void
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
    public function remove(ValidationFile $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param int $id
     * @return ValidationFile
     */
    public function findWithRelations(int $id): ValidationFile
    {
        return $this->createQueryBuilder('file')
            ->andWhere('file.id = :id')
                ->setParameter('id', $id)
            ->innerJoin('file.job', 'job')
                ->addSelect('job')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
