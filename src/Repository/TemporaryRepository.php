<?php

namespace App\Repository;

use App\Entity\Temporary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Temporary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temporary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temporary[]    findAll()
 * @method Temporary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemporaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temporary::class);
    }

    // /**
    //  * @return Temporary[] Returns an array of Temporary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Temporary
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
