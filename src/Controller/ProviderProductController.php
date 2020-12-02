<?php

namespace App\Controller;

use App\Entity\ProviderProduct;
use App\Form\ProviderProductType;
use App\Repository\ProviderProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/provider/product")
 */
class ProviderProductController extends AbstractController
{
    /**
     * @Route("/", name="provider_product_index", methods={"GET"})
     */
    public function index(ProviderProductRepository $providerProductRepository): Response
    {
        return $this->render('provider_product/index.html.twig', [
            'provider_products' => $providerProductRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="provider_product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $providerProduct = new ProviderProduct();
        $form = $this->createForm(ProviderProductType::class, $providerProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($providerProduct);
            $entityManager->flush();

            return $this->redirectToRoute('provider_product_index');
        }

        return $this->render('provider_product/new.html.twig', [
            'provider_product' => $providerProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_product_show", methods={"GET"})
     */
    public function show(ProviderProduct $providerProduct): Response
    {
        return $this->render('provider_product/show.html.twig', [
            'provider_product' => $providerProduct,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="provider_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProviderProduct $providerProduct): Response
    {
        $form = $this->createForm(ProviderProductType::class, $providerProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('provider_product_index');
        }

        return $this->render('provider_product/edit.html.twig', [
            'provider_product' => $providerProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProviderProduct $providerProduct): Response
    {
        if ($this->isCsrfTokenValid('delete'.$providerProduct->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($providerProduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provider_product_index');
    }
}
