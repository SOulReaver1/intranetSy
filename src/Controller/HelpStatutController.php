<?php

namespace App\Controller;

use App\Entity\HelpStatut;
use App\Form\HelpStatutType;
use App\Repository\HelpStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dev/help/statut")
 */
class HelpStatutController extends AbstractController
{
    /**
     * @Route("/", name="help_statut_index", methods={"GET"})
     */
    public function index(HelpStatutRepository $helpStatutRepository): Response
    {
        return $this->render('help_statut/index.html.twig', [
            'help_statuts' => $helpStatutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="help_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $helpStatut = new HelpStatut();
        $form = $this->createForm(HelpStatutType::class, $helpStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($helpStatut);
            $entityManager->flush();

            return $this->redirectToRoute('help_statut_index');
        }

        return $this->render('help_statut/new.html.twig', [
            'help_statut' => $helpStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="help_statut_show", methods={"GET"})
     */
    public function show(HelpStatut $helpStatut): Response
    {
        return $this->render('help_statut/show.html.twig', [
            'help_statut' => $helpStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="help_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HelpStatut $helpStatut): Response
    {
        $form = $this->createForm(HelpStatutType::class, $helpStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('help_statut_index');
        }

        return $this->render('help_statut/edit.html.twig', [
            'help_statut' => $helpStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="help_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, HelpStatut $helpStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$helpStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($helpStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('help_statut_index');
    }
}
