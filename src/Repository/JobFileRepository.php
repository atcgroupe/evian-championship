<?php

namespace App\Repository;

use App\Entity\JobFile;
use App\Entity\ValidationFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobFile>
 *
 * @method JobFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobFile[]    findAll()
 * @method JobFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobFile::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(JobFile $entity, bool $flush = false): void
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
    public function remove(JobFile $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param int $id
     * @return JobFile
     */
    public function findWithRelations(int $id): JobFile
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
