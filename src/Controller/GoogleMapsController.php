<?php

namespace App\Controller;

use App\Repository\CustomerFilesStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleMapsController extends AbstractController
{
    /**
     * @Route("/maps", name="google_maps")
     */
    public function index(CustomerFilesStatutRepository $statut)
    {
        return $this->render('google_maps/index.html.twig', [
            'statuts' => $statut->findAll()
        ]);
    }

    /**
     * @Route("/maps/addresses", name="google_maps_addresses", methods={"POST"})
    */
    public function getAddresses(): object {
        return new JsonResponse(['3 rue rené boulanger']);
    }
}