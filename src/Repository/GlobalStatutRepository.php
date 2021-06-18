<?php

namespace App\Repository;

use App\Entity\GlobalStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GlobalStatut|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalStatut|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalStatut[]    findAll()
 * @method GlobalStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalStatut::class);
    }

    // /**
    //  * @return GlobalStatut[] Returns an array of GlobalStatut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GlobalStatut
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
