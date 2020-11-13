<?php

namespace App\Controller;

use App\Entity\ClientStatut;
use App\Form\ClientStatutType;
use App\Repository\ClientStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client/statut")
 */
class ClientStatutController extends AbstractController
{
    /**
     * @Route("/", name="client_statut_index", methods={"GET"})
     */
    public function index(ClientStatutRepository $clientStatutRepository): Response
    {
        return $this->render('client_statut/index.html.twig', [
            'client_statuts' => $clientStatutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="client_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $clientStatut = new ClientStatut();
        $form = $this->createForm(ClientStatutType::class, $clientStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clientStatut);
            $entityManager->flush();

            return $this->redirectToRoute('client_statut_index');
        }

        return $this->render('client_statut/new.html.twig', [
            'client_statut' => $clientStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_show", methods={"GET"})
     */
    public function show(ClientStatut $clientStatut): Response
    {
        return $this->render('client_statut/show.html.twig', [
            'client_statut' => $clientStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ClientStatut $clientStatut): Response
    {
        $form = $this->createForm(ClientStatutType::class, $clientStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_statut_index');
        }

        return $this->render('client_statut/edit.html.twig', [
            'client_statut' => $clientStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ClientStatut $clientStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clientStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_statut_index');
    }
}
