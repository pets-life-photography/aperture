<?php

namespace App\Repository;

use App\Entity\PhotoType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PhotoType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoType[]    findAll()
 * @method PhotoType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PhotoType::class);
    }

    // /**
    //  * @return PhotoType[] Returns an array of PhotoType objects
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
    public function findOneBySomeField($value): ?PhotoType
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
