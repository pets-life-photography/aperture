<?php

namespace App\Repository;

use App\Entity\WatermarkConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WatermarkConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method WatermarkConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method WatermarkConfiguration[]    findAll()
 * @method WatermarkConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatermarkConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WatermarkConfiguration::class);
    }

    // /**
    //  * @return WatermarkConfiguration[] Returns an array of WatermarkConfiguration objects
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
    public function findOneBySomeField($value): ?WatermarkConfiguration
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
