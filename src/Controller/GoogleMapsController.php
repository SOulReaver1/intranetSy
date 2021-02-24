<?php

namespace App\Controller;

use App\Repository\CustomerFilesRepository;
use App\Repository\CustomerFilesStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleMapsController extends AbstractController
{
    /**
     * @Route("/maps", name="google_maps")
     */
    public function index(CustomerFilesStatutRepository $statut, CustomerFilesRepository $repository)
    {
        // if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
        //     return $this->render('google_maps/index.html.twig', [
        //         'statuts' => $statut->googleMaps(),
        //         'nullCount' => $repository->countNullFileStatut()['count']
        //     ]);
        // }
    
        return $this->render('google_maps/index.html.twig', [
            'statuts' => $statut->googleMaps(),
            'nullCount' => $repository->countNullFileStatut()['count']
        ]);
    }
    
    /**
     * @Route("/maps/addresses", name="google_maps_addresses", methods={"POST"})
    */
    public function getAddresses(CustomerFilesRepository $repository): object {

        if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
            return new JsonResponse($repository->getAddresses($this->getUser()));
        }
        
        return new JsonResponse($repository->getAddresses());
    }
}