<?php

namespace App\Repository;

use App\Entity\Deals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deals|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deals|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deals[]    findAll()
 * @method Deals[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deals::class);
    }

    // /**
    //  * @return Deals[] Returns an array of Deals objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deals
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
