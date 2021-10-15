<?php

namespace App\Controller;

use App\Entity\ClientStatutDocument;
use App\Entity\GlobalStatut;
use App\Form\ClientStatutDocumentType;
use Doctrine\ORM\QueryBuilder;
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
 * @Route("/customers/{global}/client/document", requirements={"global":"\d+"})
 */
class ClientStatutDocumentController extends AbstractController
{

    private $global;

    /**
     * @Route("/", name="client_statut_document_index", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatut $global, DataTableFactory $dataTableFactory): Response
    {
        $this->global = $global;

        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('client_statut', TextColumn::class, [
            'data' => function($context){
                $client_statut = array();
                foreach ($context->getClientStatut() as $value) {
                    $client_statut[] = $value->getName();
                }
                return implode(', ', $client_statut);
            },
            'label' => 'Statut(s)'
        ])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Date de création', 'format' => 'd-m-Y H:i:s', 'searchable' => false])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('client_statut_document_show', ['global' => $this->global->getId(), 'id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('client_statut_document_edit', ['global' => $this->global->getId(), 'id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => ClientStatutDocument::class,
            'query' => function(QueryBuilder $builder){
                return $builder
                ->select('c')
                ->where('c.global_statut = :g')
                ->setParameter('g', $this->global)
                ->from(ClientStatutDocument::class, 'c');
            }
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('client_statut_document/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="client_statut_document_new", methods={"GET","POST"})
     */
    public function new(Request $request, GlobalStatut $global): Response
    {
        $clientStatutDocument = new ClientStatutDocument();
        $form = $this->createForm(ClientStatutDocumentType::class, $clientStatutDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientStatutDocument->setGlobalStatut($global);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($clientStatutDocument);
            $entityManager->flush();

            return $this->redirectToRoute('client_statut_document_index', [
                'global' => $global->getId()
            ]);
        }

        return $this->render('client_statut_document/new.html.twig', [
            'client_statut_document' => $clientStatutDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_document_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(ClientStatutDocument $clientStatutDocument): Response
    {
        return $this->render('client_statut_document/show.html.twig', [
            'client_statut_document' => $clientStatutDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_statut_document_edit", methods={"GET","POST"}, requirements={"global":"\d+"})
     */
    public function edit(Request $request, GlobalStatut $global, ClientStatutDocument $clientStatutDocument): Response
    {
        $form = $this->createForm(ClientStatutDocumentType::class, $clientStatutDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_statut_document_index', [
                'global' => $global->getId()
            ]);
        }

        return $this->render('client_statut_document/edit.html.twig', [
            'client_statut_document' => $clientStatutDocument,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="client_statut_document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GlobalStatut $global, ClientStatutDocument $clientStatutDocument): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientStatutDocument->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clientStatutDocument);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_statut_document_index', [
            'global' => $global->getId()
        ]);
    }
}
