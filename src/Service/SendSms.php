<?php

namespace App\Service;

use DateInterval;
use DateTime;
use Ovh\Api;

class SendSms {

    private $applicationKey;
    private $applicationSecret;
    private $consumerKey;
    private $endpoint;
    private $sms;
    private $services;

    public function __construct()
    {
        $this->applicationKey = $_ENV["OVH_APPLICATION_KEY"];
        $this->applicationSecret =  $_ENV["OVH_APPLICATION_SECRET"];
        $this->consumerKey =  $_ENV["OVH_CUSTOMER_KEY"];
        $this->endpoint = 'ovh-eu';
        $this->sms = new Api($this->applicationKey, $this->applicationSecret, $this->endpoint, $this->consumerKey);
        $this->services = $this->sms->get('/sms');
    }

    public function getOutgoings(){
        return $this->sms->get('/sms/'.$this->services[0].'/outgoing');
    }

    public function getOutGoing(int $id){
        return $this->sms->get('/sms/'.$this->services[0].'/outgoing/'.$id);
    }

    public function getOutGoinsAsArray(){
        $outgoings = [];
        foreach ($this->getOutgoings() as $value) {
            $outgoings[] = $this->getOutGoing($value);
        }
        return $outgoings;
    }

    public function deleteOutGoing(int $id){
        return $this->sms->delete('/sms/'.$this->services[0].'/outgoing/'.$id);
    }

    public function getJobs(){
        return $this->sms->get('/sms/'.$this->services[0].'/jobs');
    }

    public function getJob(int $id){
        return $this->sms->get('/sms/'.$this->services[0].'/jobs/'.$id);
    }

    public function getJobsAsArray(){
        $jobs = [];
        foreach ($this->getJobs() as $value) {
            $jobs[] = $this->getJob($value);
        }
        return $jobs;
    }

    public function deleteJob(int $id){
        return $this->sms->delete('/sms/'.$this->services[0].'/jobs/'.$id);
    }

    public function send(string $content, array $phoneNumber, Datetime $metrage = null, int $interval = null){ 
        if($metrage && $interval){
            $now = new DateTime('now');
            $diff = ($metrage)->getTimestamp() - ($now)->getTimestamp();
            $minutes = intval($diff/60);
            $intervalInMinutes = $minutes - $interval;
            if($intervalInMinutes < 0) return;
        }
        $content = (object) array(
            "charset"=> "UTF-8",
            "class"=> "phoneDisplay",
            "coding"=> "7bit",
            "message"=> $content,
            "noStopClause"=> false,
            "priority"=> "high",
            "receivers"=> ["
            +33652846684"],
            "differedPeriod" => $intervalInMinutes ?? 0,
            "senderForResponse"=> true,
            "validityPeriod"=> 2880
        );
        $resultPostJob = $this->sms->post('/sms/'. $this->services[0] . '/jobs', $content);
        $smsJobs = $this->sms->get('/sms/'. $this->services[0] . '/jobs');
    }
}