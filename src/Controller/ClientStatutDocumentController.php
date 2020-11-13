<?php

namespace App\Controller;

use App\Entity\ClientStatutDocument;
use App\Form\ClientStatutDocumentType;
use App\Repository\ClientStatutDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client/document")
 */
class ClientStatutDocumentController extends AbstractController
{
    /**
     * @Route("/", name="client_statut_document_index", methods={"GET"})
     */
    public function index(ClientStatutDocumentRepository $clientStatutDocumentRepository): Response
    {
        return $this->render('client_statut_document/index.html.twig', [
            'client_statut_documents' => $clientStatutDocumentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="client_statut_document_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $clientStatutDocument = new ClientStatutDocument();
        $form = $this->createForm(ClientStatutDocumentType::class, $clientStatutDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clientStatutDocument);
            $entityManager->flush();

            return $this->redirectToRoute('client_statut_document_index');
        }

        return $this->render('client_statut_document/new.html.twig', [
            'client_statut_document' => $clientStatutDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_document_show", methods={"GET"})
     */
    public function show(ClientStatutDocument $clientStatutDocument): Response
    {
        return $this->render('client_statut_document/show.html.twig', [
            'client_statut_document' => $clientStatutDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_statut_document_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ClientStatutDocument $clientStatutDocument): Response
    {
        $form = $this->createForm(ClientStatutDocumentType::class, $clientStatutDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_statut_document_index');
        }

        return $this->render('client_statut_document/edit.html.twig', [
            'client_statut_document' => $clientStatutDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ClientStatutDocument $clientStatutDocument): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientStatutDocument->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clientStatutDocument);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_statut_document_index');
    }
}
