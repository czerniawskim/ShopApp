<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    private function getBestsellers()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.sold', 'DESC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    private function getNewProducts()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    private function getLowStock()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.stock')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    private function getBestRated()
    {
        $data = $this->createQueryBuilder('p')->getQuery()->getResult();
        $rated = $rateSum = [];

        foreach ($data as $d) {
            if (sizeof($d->getRating()) > 0) {
                $rated[] = $d;
            }

        }
        unset($data);
        unset($d);

        foreach ($rated as $r) {
            $total = 0;
            $size = sizeof($r->getRating());
            foreach ($r->getRating() as $rate) {
                $total += $rate['grade'];
            }

            $rateSum[] = ['row' => $r, 'total' => $total, 'amount' => $size, 'score' => $total / $size];
        }

        usort($rateSum, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $rated = [];

        for ($i = 0; $i < 15; $i++) {
            $rated[] = $rateSum[$i]['row'];
        }

        return $rated;
    }

    public function getHomeData()
    {
        return ["Bestsellers" => $this->getBestsellers(), "New products" => $this->getNewProducts(), "Get untill available" => $this->getLowStock(), "Best rated" => $this->getBestRated()];
    }

    public function getQueried(string $query)
    {
        return $this->createQueryBuilder('p')
            ->andWhere("p.name LIKE :query or p.description LIKE :query")
            ->setParameter(":query", "%$query%")
            ->orderBy("p.name")
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('p')
    ->andWhere('p.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('p.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Products
{
return $this->createQueryBuilder('p')
->andWhere('p.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
