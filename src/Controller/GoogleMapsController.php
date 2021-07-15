<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
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
     * @Route("/{id}", name="google_customer_redirect", methods={"GET"}, requirements={"id":"\d+"})
    */
    public function redirectToCustomer(Request $request, GlobalStatut $global, CustomerFiles $customerFile) {
        return $this->redirectToRoute('customer_files_show', [
            'id' => $customerFile->getId(),
            'global' => $global->getId()
        ]);
    }
    /**
     * @Route("/{id}/commentary", name="customer_file_change_commentary", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function changeCommentary(Request $request, CustomerFiles $customerFile): object {
        $data = json_decode($request->getContent(), true);
        $commentary = $data['commentary'] ?? null;
        if($commentary){
            $customerFile->setCommentary($commentary);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['status' => 200]);

        }
        return new JsonResponse(['status' => 404]);

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