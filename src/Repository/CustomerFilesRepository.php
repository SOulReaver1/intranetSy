<?php

namespace App\Repository;

use App\Entity\CustomerFiles;
use App\Entity\GlobalStatut;
use App\Entity\Provider;
use App\Entity\User;
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

    public function getProviderParams(?Provider $provider) {
        $parameters = [];
        if($provider !== null){
            foreach($provider->getProviderProducts() as $value){
                foreach($value->getParams() as $param){
                    $parameters[] = $param;
                }
            }
        }
        
        return array_unique($parameters);
    }

    public function getAllReplaceFields(): array {
        return array_merge($this->getComplementFields(), $this->getStepFields());
    }

    public function checkStepIsOk(CustomerFiles $customerFiles, array $fields){
        $result = $this->createQueryBuilder('c')
            ->andWhere('c = :c')
            ->setParameter('c', $customerFiles)
        ;
        foreach ($fields as $field) {
            $result->addSelect("c.$field")->andWhere("c.$field IS NOT NULL");
        }

        return $result
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getComplementFields(): array {
        $array = [
            "sexe" => "Sexe",
            "name" => "Nom",
            "global_statut" => "Statut global",
            "client_statut" => "Statut client",
            "documents" => "Documents récquis :"
        ];

        return $array;
    }

    public function getStepFields(): array {
        $array = [
            "address" => "Adresse",
            "home_phone" => "Téléphone fixe",
            "cellphone" => "Téléphone portable",
            "referent_name" => "Nom du référent",
            "referent_phone" => "Téléphone du référent",
            "referent_statut" => "Statut du référent",
            "customer_statut" => "Statut du dossier",
            "client_statut" => 'Statut du client',
            "mail_al" => "Mail AL",
            "password_al" => "Mot de passe AL",
            "address_complement" => "Complément d'adresse",
            "installer" => "Installateur",
            "product" => "Produit",
            "acompte" => "Acompte",
            "solde" => "Solde",
            "invoice_number" => "Numéro de facture",
            "dossier_number" => "Numéro de dossier",
            "date_depot" => "Date de dépot",
            "date_cmd_materiel" => "Date de commande de matériel",
            "date_install" => "Date d'installation",
            "date_expertise" => "Date d'éxpertise",
            "date_footage" => "Date de métrage",
            "metreur" => "Métreur",
        ];

        return $array;
    }

    public function getAddresses(GlobalStatut $global, ?User $user = null){
        if($user){
            return $this->createQueryBuilder('c')
            ->leftJoin('c.customer_statut', 'statut')
            ->select('c.id, c.name as title, c.address, c.route_number, c.zip_code, c.city, c.cellphone, c.home_phone, c.commentary, c.address_complement, c.lat, c.lng, statut.color, statut.id as statutId')
            ->leftJoin('c.installer', 'user')
            ->andWhere('user = :user')
            ->andWhere('c.global_statut = :g')
            ->setParameter('g', $global)
            ->setParameter('user', $user)
            ->orderBy('c.ordred')
            ->andWhere('c.lat is not null')
            ->andWhere('c.lng is not null')
            ->getQuery()
            ->getResult();
        }

        return $this->createQueryBuilder('c')
        ->leftJoin('c.customer_statut', 'statut')
        ->select('c.id, c.name as title, c.address, c.route_number, c.zip_code, c.city, c.address_complement, c.cellphone, c.home_phone, c.commentary, c.lat, c.lng, statut.color, statut.id as statutId')
        ->andWhere('c.lat is not null')
        ->andWhere('c.lng is not null')
        ->andWhere('c.global_statut = :g')
        ->setParameter('g', $global)
        ->getQuery()
        ->getResult();
    }

    public function getPhones(){
        return $this->createQueryBuilder('c')
        ->select('c.name as title, c.cellphone')
        ->where('c.cellphone IS NOT NULL')
        ->getQuery()
        ->getResult();
    }

    public function getInstaller($installer){
        return $this->createQueryBuilder('c')
        ->leftJoin('c.installer', 'user')
        ->where('user = :user')
        ->setParameter('user', $installer)
        ->getQuery()
        ->getResult();
    }

    public function findByStatut($id){
        return $this->createQueryBuilder('c')
        ->leftJoin('c.customer_statut', 'statut')
        ->where('statut.id = :statut')
        ->setParameter("statut", $id)
        ->getQuery()
        ->getResult();
    }

    public function countNullFileStatut(GlobalStatut $global){
        return $this->createQueryBuilder('c')
        ->leftJoin('c.customer_statut', 'statut')
        ->andWhere('c.address IS NOT NULL')
        ->andWhere('statut.id IS NULL')
        ->andWhere('c.global_statut = :g')
        ->setParameter('g', $global)
        ->select('count(c.id) as count')
        ->getQuery()->getResult()[0];
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
