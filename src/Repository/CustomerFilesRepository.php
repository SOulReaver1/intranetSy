<?php

namespace App\Repository;

use App\Entity\CustomerFiles;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method CustomerFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerFiles[]    findAll()
 * @method CustomerFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerFiles::class);
    }

    public function getProviderParams(Provider $provider): array {
        $parameters = [];
        foreach($provider->getProviderProducts() as $value){
            foreach($value->getParams() as $param){
                $parameters[$param->getId()] = $param->getName();
            }
        }
        return array_unique($parameters);
    }

    // /**
    //  * @return CustomerFiles[] Returns an array of CustomerFiles objects
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
    public function findOneBySomeField($value): ?CustomerFiles
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
