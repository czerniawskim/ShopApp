<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    public function getCategories()
    {
        $data = $this->createQueryBuilder('c')
            ->select('c.name')
            ->orderBy("c.name")
            ->getQuery()
            ->getResult();

        $grouped = [];

        foreach ($data as $d) {
            $grouped[strtolower(substr($d['name'], 0, 1))][] = $d;
        }

        return $grouped;
    }

    public function getQueried(string $query)
    {
        return $this->createQueryBuilder('c')
            ->select("c.name")
            ->andWhere("c.name LIKE :query")
            ->setParameter(":query", "%$query%")
            ->orderBy("c.name")
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Categories[] Returns an array of Categories objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('c')
    ->andWhere('c.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('c.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Categories
{
return $this->createQueryBuilder('c')
->andWhere('c.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
