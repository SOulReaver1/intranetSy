<?php

namespace App\Service;

use App\Entity\Sms;
use App\Entity\SmsAuto;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ovh\Api;

class SendSms
{
    private $applicationKey;
    private $applicationSecret;
    private $consumerKey;
    private $endpoint;
    private $sms;
    private $em;
    private $services;

    // public function __construct(EntityManagerInterface $manager)
    // {
    //     $this->applicationKey = $_ENV["OVH_APPLICATION_KEY"];
    //     $this->applicationSecret =  $_ENV["OVH_APPLICATION_SECRET"];
    //     $this->consumerKey =  $_ENV["OVH_CUSTOMER_KEY"];
    //     $this->endpoint = 'ovh-eu';
    //     $this->sms = new Api($this->applicationKey, $this->applicationSecret, $this->endpoint, $this->consumerKey);
    //     $this->services = $this->sms->get('/sms');
    //     $this->em = $manager;
    // }

    // public function getOutgoings(){
    //     return $this->sms->get('/sms/'.$this->services[0].'/outgoing');
    // }

    // public function getOutGoing(int $id){
    //     return $this->sms->get('/sms/'.$this->services[0].'/outgoing/'.$id);
    // }

    // public function getOutGoinsAsArray(){
    //     $outgoings = [];
    //     foreach ($this->getOutgoings() as $value) {
    //         $outgoings[] = $this->getOutGoing($value);
    //     }
    //     return $outgoings;
    // }

    // public function deleteOutGoing(int $id){
    //     return $this->sms->delete('/sms/'.$this->services[0].'/outgoing/'.$id);
    // }

    // public function getJobs(){
    //     return $this->sms->get('/sms/'.$this->services[0].'/jobs');
    // }

    // public function getJob(int $id){
    //     return $this->sms->get('/sms/'.$this->services[0].'/jobs/'.$id);
    // }

    // public function getJobsAsArray(){
    //     $jobs = [];
    //     foreach ($this->getJobs() as $value) {
    //         $jobs[] = $this->getJob($value);
    //     }
    //     return $jobs;
    // }

    // public function deleteJob(int $id){
    //     return $this->sms->delete('/sms/'.$this->services[0].'/jobs/'.$id);
    // }

    // public function send(string $message, array $phoneNumber, SmsAuto $smsAuto, Datetime $metrage = null, int $interval = null){
    //     $now = new DateTime('now');
    //     if($metrage && $interval){
    //         $dateInterval = new DateInterval("PT$interval"."M");
    //         $diff = ($metrage)->getTimestamp() - ($now)->getTimestamp();
    //         $minutes = intval($diff/60);
    //         $intervalInMinutes = $minutes - $interval;
    //         if($intervalInMinutes < 0) return;
    //     }
    //     $content = (object) array(
    //         "charset"=> "UTF-8",
    //         "class"=> "phoneDisplay",
    //         "coding"=> "7bit",
    //         "message"=> $message,
    //         "noStopClause"=> false,
    //         "priority"=> "high",
    //         "receivers"=> $phoneNumber,
    //         "differedPeriod" => $intervalInMinutes ?? 0,
    //         "senderForResponse"=> true,
    //         "validityPeriod"=> 2880
    //     );
    //     foreach ($phoneNumber as $value) {
    //         $sms = new Sms();
    //         $sms->setSmsAuto($smsAuto);
    //         $sms->setContent($message);
    //         $sms->setPhoneNumber($value);
    //         $sms->setSendAt(isset($intervalInMinutes) ? $metrage->sub($dateInterval) : $now);
    //         $this->em->persist($sms);
    //         $this->em->flush();
    //     }
    //     $this->sms->post('/sms/'. $this->services[0] . '/jobs', $content);
    // }
}
