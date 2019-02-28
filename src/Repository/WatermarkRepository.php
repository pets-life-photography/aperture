<?php

namespace App\Repository;

use App\Entity\Watermark;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Watermark|null find($id, $lockMode = null, $lockVersion = null)
 * @method Watermark|null findOneBy(array $criteria, array $orderBy = null)
 * @method Watermark[]    findAll()
 * @method Watermark[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatermarkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Watermark::class);
    }

    // /**
    //  * @return Watermark[] Returns an array of Watermark objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Watermark
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
