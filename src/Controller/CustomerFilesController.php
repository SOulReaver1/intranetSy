<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Form\CustomerFilesType;
use App\Repository\CustomerFilesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class CustomerFilesController extends AbstractController
{
    /**
     * @Route("/", name="customer_files_index", methods={"GET"})
     */
    public function index(CustomerFilesRepository $customerFilesRepository): Response
    {
        return $this->render('customer_files/index.html.twig', [
            'customer_files' => $customerFilesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_files_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $customerFile = new CustomerFiles();
        $form = $this->createForm(CustomerFilesType::class, $customerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFile);
            $entityManager->flush();

            return $this->redirectToRoute('customer_files_index');
        }

        return $this->render('customer_files/new.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_files_show", methods={"GET"})
     */
    public function show(CustomerFiles $customerFile): Response
    {
        return $this->render('customer_files/show.html.twig', [
            'customer_file' => $customerFile,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_files_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomerFiles $customerFile): Response
    {
        $form = $this->createForm(CustomerFilesType::class, $customerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_files_index');
        }

        return $this->render('customer_files/edit.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_files_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomerFiles $customerFile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerFile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('customer_files_index');
    }
}
