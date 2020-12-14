<?php

namespace App\Controller;

use App\Repository\CustomerFilesRepository;
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
    public function index(CustomerFilesStatutRepository $statut, CustomerFilesRepository $repository)
    {
        return $this->render('google_maps/index.html.twig', [
            'statuts' => $statut->findAll()
        ]);
    }

    /**
     * @Route("/maps/addresses", name="google_maps_addresses", methods={"POST"})
    */
    public function getAddresses(CustomerFilesRepository $repository): object {
        return new JsonResponse($repository->getAddresses());
    }
}