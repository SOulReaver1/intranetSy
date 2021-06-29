<?php

namespace App\Controller;

use App\Entity\GlobalStatut;
use App\Repository\CustomerFilesRepository;
use App\Repository\CustomerFilesStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/customers/{global}/maps", requirements={"global":"\d+"})
 */
class GoogleMapsController extends AbstractController
{
    /**
     * @Route("/", name="google_maps")
     */
    public function index(Request $request, GlobalStatut $global, CustomerFilesStatutRepository $statut, CustomerFilesRepository $repository)
    {
        if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
            return $this->render('google_maps/index.html.twig', [
                'statuts' => $statut->googleMaps($global),
                'nullCount' => $repository->countNullFileStatut($global)['count']
            ]);
        }
    
        return $this->render('google_maps/index.html.twig', [
            'statuts' => $statut->googleMaps($global),
            'nullCount' => $repository->countNullFileStatut($global)['count']
        ]);
    }
    
    /**
     * @Route("/addresses", name="google_maps_addresses", methods={"POST"})
    */
    public function getAddresses(GlobalStatut $global, CustomerFilesRepository $repository): object {

        if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
            return new JsonResponse($repository->getAddresses($this->getUser()));
        }
        
        return new JsonResponse($repository->getAddresses($global));
    }
}