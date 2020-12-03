<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleMapsController extends AbstractController
{
    /**
     * @Route("/maps", name="google_maps")
     */
    public function index()
    {
        return $this->render('google_maps/index.html.twig', [
            'addresses' => ['3 rue René Boulanger', '18 rue de la tuilerie']
        ]);
    }

    /**
     * @Route("/maps/addresses", name="google_maps_addresses", methods={"POST"})
    */
    public function getAddresses(): object {
        return new JsonResponse(['3 rue rené boulanger']);
    }
}