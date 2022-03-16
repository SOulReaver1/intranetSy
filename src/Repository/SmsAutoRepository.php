<?php

namespace App\Repository;

use App\Entity\CustomerFiles;
use App\Entity\SmsAuto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SmsAuto|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsAuto|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsAuto[]    findAll()
 * @method SmsAuto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsAutoRepository extends ServiceEntityRepository
{
    private $em;
    private $sendSms;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
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

    // public function checkSms(CustomerFiles $customerFiles): array
    // {
    //     if ($customerFiles->getMessage()) {
    //         if ($customerFiles->getCellphone()) {
    //             $smsAuto = $this->findBy([
    //                 'global_statut' => $customerFiles->getGlobalStatut(),
    //             ]);
    //             $validSms = [];
    //             $customerRepo = $this->em->getRepository(CustomerFiles::class);
    //             $allFields = array_keys($customerRepo->getAllReplaceFields());
    //             foreach ($smsAuto as $value) {
    //                 $alreadySend = false;
    //                 foreach ($value->getSms() as $sms) {
    //                     if (
    //                         $sms->getPhoneNumber() ===
    //                         $customerFiles->getCellphone()
    //                     ) {
    //                         $alreadySend = true;
    //                     }
    //                 }
    //                 if (!$alreadySend) {
    //                     $fields = array_intersect(
    //                         $value->getFields(),
    //                         $allFields
    //                     );
    //                     $result = $customerRepo->checkStepIsOk(
    //                         $customerFiles,
    //                         $fields
    //                     );
    //                     $fields = array_merge(
    //                         $fields,
    //                         array_flip($customerRepo->getComplementFields())
    //                     );
    //                     if ($result) {
    //                         $patterns = [];
    //                         $pattern_replace = [];
    //                         $result['name'] = $result[0]->getName();
    //                         $result['sexe'] = $result[0]->getSexe();
    //                         $result[
    //                             'global_statut'
    //                         ] = $result[0]->getGlobalStatut()->getName();
    //                         $result[
    //                             'client_statut'
    //                         ] = $result[0]->getClientStatut()->getName();
    //                         $result['documents'] = implode(
    //                             ', ',
    //                             $result[0]
    //                                 ->getClientStatut()
    //                                 ->getClientStatutDocuments()
    //                                 ->toArray()
    //                         );
    //                         foreach ($fields as $field) {
    //                             $v = '{' . $field . '}';
    //                             array_push($patterns, "/$v/");
    //                             array_push($pattern_replace, $result[$field]);
    //                         }
    //                         $content = preg_replace(
    //                             $patterns,
    //                             $pattern_replace,
    //                             $value->getContent()
    //                         );
    //                         // $this->sendSms->send(
    //                         //     $content,
    //                         //     [$customerFiles->getCellphone()],
    //                         //     $value
    //                         // );
    //                         array_push($validSms, $content);
    //                     }
    //                 }
    //             }

    //             return $validSms;
    //         }

    //         return [];
    //     }

    //     return [];
    // }
}
