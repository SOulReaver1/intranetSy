<?php

namespace App\Controller;

use App\Entity\ClientStatut;
use App\Form\ClientStatutType;
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
 * @Route("/client/statut")
 */
class ClientStatutController extends AbstractController
{
    /**
     * @Route("/", name="client_statut_index", methods={"GET", "POST"})
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
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('client_statut_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('client_statut_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => ClientStatut::class
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('client_statut/index.html.twig', [
            "datatable" => $table
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
