<?php

namespace App\Repository;

use App\Entity\ProviderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProviderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderProduct[]    findAll()
 * @method ProviderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderProduct::class);
    }

    /**
      * @return ProviderProduct[] Returns an array of ProviderProduct objects
    */
    public function findByProductParam($params, $provider)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT provider_product.id, provider_product.name from provider_product inner join provider_product_provider_param pa on pa.provider_product_id = provider_product.id where provider_product.provider_id = :providerId AND pa.provider_param_id in (:val)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'val' => implode(', ', $params),
            "providerId" => $provider
        ]);
        return $stmt->fetchAll();
    }

    /*
    public function findOneBySomeField($value): ?ProviderProduct
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
