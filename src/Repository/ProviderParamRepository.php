<?php

namespace App\Repository;

use App\Entity\ProviderParam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProviderParam|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderParam|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderParam[]    findAll()
 * @method ProviderParam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderParamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderParam::class);
    }

    // /**
    //  * @return ProviderParam[] Returns an array of ProviderParam objects
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
    public function findOneBySomeField($value): ?ProviderParam
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
