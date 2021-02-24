<?php

namespace App\Controller;

use App\Entity\ProviderParam;
use App\Form\ProviderParamType;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/provider/param")
 */
class ProviderParamController extends AbstractController
{
    /**
     * @Route("/", name="provider_param_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Date de création', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('provider_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('provider_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => ProviderParam::class
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('provider_param/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="provider_param_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $providerParam = new ProviderParam();
        $form = $this->createForm(ProviderParamType::class, $providerParam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($providerParam);
            $entityManager->flush();

            return $this->redirectToRoute('provider_param_index');
        }

        return $this->render('provider_param/new.html.twig', [
            'provider_param' => $providerParam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_param_show", methods={"GET"})
     */
    public function show(ProviderParam $providerParam): Response
    {
        return $this->render('provider_param/show.html.twig', [
            'provider_param' => $providerParam,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="provider_param_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProviderParam $providerParam): Response
    {
        $form = $this->createForm(ProviderParamType::class, $providerParam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('provider_param_index');
        }

        return $this->render('provider_param/edit.html.twig', [
            'provider_param' => $providerParam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_param_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProviderParam $providerParam): Response
    {
        if ($this->isCsrfTokenValid('delete'.$providerParam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($providerParam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provider_param_index');
    }
}
