<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification/{id}", name="notification", methods={"DELETE"})
     */
    public function delete(Request $request, Notification $notification)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $notification->removeUser($this->getUser());
        $entityManager->flush();
        return new JsonResponse([]);
    } 
}
