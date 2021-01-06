<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;

class NotificationService {

    private $em;
    private $security;

    public function __construct(EntityManager $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function sendNotification(array $users, string $title, string $url, ?string $description = null){
        if(in_array($this->security->getUser(), $users)){
            $index = array_search($this->security->getUser(), $users);
            unset($users[$index]);
        }
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setUrl($url);
        $description && $notification->setDescription($description);
        $notification->addUser(...$users);
        $this->em->persist($notification);
        $this->em->flush();
    }

}