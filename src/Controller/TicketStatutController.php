<?php

namespace App\Controller;

use App\Entity\TicketStatut;
use App\Form\TicketStatutType;
use App\Repository\TicketStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket/statut")
 */
class TicketStatutController extends AbstractController
{
    /**
     * @Route("/", name="ticket_statut_index", methods={"GET"})
     */
    public function index(TicketStatutRepository $ticketStatutRepository): Response
    {
        return $this->render('ticket_statut/index.html.twig', [
            'ticket_statuts' => $ticketStatutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ticket_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ticketStatut = new TicketStatut();
        $form = $this->createForm(TicketStatutType::class, $ticketStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticketStatut);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_statut_index');
        }

        return $this->render('ticket_statut/new.html.twig', [
            'ticket_statut' => $ticketStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_statut_show", methods={"GET"})
     */
    public function show(TicketStatut $ticketStatut): Response
    {
        return $this->render('ticket_statut/show.html.twig', [
            'ticket_statut' => $ticketStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TicketStatut $ticketStatut): Response
    {
        $form = $this->createForm(TicketStatutType::class, $ticketStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_statut_index');
        }

        return $this->render('ticket_statut/edit.html.twig', [
            'ticket_statut' => $ticketStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TicketStatut $ticketStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticketStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticketStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_statut_index');
    }
}
