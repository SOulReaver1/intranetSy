<?php

namespace App\Repository;

use App\Entity\ClientStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientStatut|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientStatut|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientStatut[]    findAll()
 * @method ClientStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientStatut::class);
    }

    // /**
    //  * @return ClientStatut[] Returns an array of ClientStatut objects
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
    public function findOneBySomeField($value): ?ClientStatut
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
