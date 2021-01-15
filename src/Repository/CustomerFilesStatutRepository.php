<?php

namespace App\Repository;

use App\Entity\CustomerFilesStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerFilesStatut|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerFilesStatut|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerFilesStatut[]    findAll()
 * @method CustomerFilesStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerFilesStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerFilesStatut::class);
    }

    public function getNbr(){
        return $this->createQueryBuilder('c')
        ->select('count(c.id)')
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function findAllByOrder(){
        return $this->createQueryBuilder('c')
        ->orderBy('c.ordered')
        ->getQuery()
        ->getResult();
    }

    public function findOneByOrder($order){
        return $this->createQueryBuilder('c')
        ->andWhere('c.ordered = :order')
        ->setParameter('order', $order)
        ->getQuery()
        ->getResult()[0];
    }

    public function googleMaps(){
        return $this->createQueryBuilder('c')
        ->leftJoin('c.customerFiles', 'fiche')
        ->select('count(fiche.id) as count, c.color, c.name, c.id')
        ->where('fiche.address IS NOT NULL')
        ->groupBy('c.id')
        ->getQuery()
        ->getResult();
    }


    // /**
    //  * @return CustomerFilesStatut[] Returns an array of CustomerFilesStatut objects
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
    public function findOneBySomeField($value): ?CustomerFilesStatut
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
