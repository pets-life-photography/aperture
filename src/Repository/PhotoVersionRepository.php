<?php

namespace App\Repository;

use App\Entity\PhotoVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PhotoVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoVersion[]    findAll()
 * @method PhotoVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoVersionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PhotoVersion::class);
    }

    // /**
    //  * @return PhotoVersion[] Returns an array of PhotoVersion objects
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
    public function findOneBySomeField($value): ?PhotoVersion
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
