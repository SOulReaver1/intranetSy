<?php

namespace App\Controller;

use App\Entity\CustomerFilesStatut;
use App\Form\CustomerFilesStatutType;
use App\Repository\CustomerFilesStatutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/customer/statut")
 */
class CustomerFilesStatutController extends AbstractController
{
    /**
     * @Route("/", name="customer_files_statut_index", methods={"GET"})
     */
    public function index(CustomerFilesStatutRepository $customerFilesStatutRepository): Response
    {
        return $this->render('customer_files_statut/index.html.twig', [
            'customer_files_statuts' => $customerFilesStatutRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_files_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $customerFilesStatut = new CustomerFilesStatut();
        $form = $this->createForm(CustomerFilesStatutType::class, $customerFilesStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFilesStatut);
            $entityManager->flush();

            $this->addFlash('success', 'Votre statut à bien été créer !');
            return $this->redirectToRoute('customer_files_statut_index');
        }

        return $this->render('customer_files_statut/new.html.twig', [
            'customer_files_statut' => $customerFilesStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_files_statut_show", methods={"GET"})
     */
    public function show(CustomerFilesStatut $customerFilesStatut): Response
    {
        return $this->render('customer_files_statut/show.html.twig', [
            'customer_files_statut' => $customerFilesStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_files_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomerFilesStatut $customerFilesStatut): Response
    {
        $form = $this->createForm(CustomerFilesStatutType::class, $customerFilesStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre statut à bien été modifier !');
            return $this->redirectToRoute('customer_files_statut_index');
        }

        return $this->render('customer_files_statut/edit.html.twig', [
            'customer_files_statut' => $customerFilesStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_files_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomerFilesStatut $customerFilesStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerFilesStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerFilesStatut);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Votre statut à bien été supprimer !');
        return $this->redirectToRoute('customer_files_statut_index');
    }
}
