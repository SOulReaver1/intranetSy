<?php

namespace App\Repository;

use App\Entity\SmsAuto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SmsAuto|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsAuto|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsAuto[]    findAll()
 * @method SmsAuto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsAutoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsAuto::class);
    }

    // /**
    //  * @return SmsAuto[] Returns an array of SmsAuto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SmsAuto
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
