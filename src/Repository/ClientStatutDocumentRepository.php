<?php

namespace App\Repository;

use App\Entity\ClientStatut;
use App\Entity\ClientStatutDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientStatutDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientStatutDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientStatutDocument[]    findAll()
 * @method ClientStatutDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientStatutDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientStatutDocument::class);
    }

    public function findStatut($customer){
        return $this->createQueryBuilder('u')
        ->leftJoin('u.client_statut', 'client') 
        ->where('client.id = :id')
        ->setParameter('id', $customer->getClientStatutId())
        ->getQuery()->getResult();
    }

    public function findDocumentsByRequired(ClientStatut $clientStatut, bool $required){
        return $this->createQueryBuilder('c')
        ->select('c.name')
        ->leftJoin('c.client_statut', 'client_statut')
        ->where('client_statut = :client')
        ->andWhere('c.required = :required')
        ->setParameter('client', $clientStatut)
        ->setParameter('required', $required)
        ->getQuery()->getresult();
    }

    // /**
    //  * @return ClientStatutDocument[] Returns an array of ClientStatutDocument objects
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
    public function findOneBySomeField($value): ?ClientStatutDocument
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
