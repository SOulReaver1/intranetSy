<?php

namespace App\Repository;

use App\Entity\TicketStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketStatut|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketStatut|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketStatut[]    findAll()
 * @method TicketStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketStatut::class);
    }

    // /**
    //  * @return TicketStatut[] Returns an array of TicketStatut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TicketStatut
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
