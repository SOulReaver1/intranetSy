<?php

namespace App\Controller;

use App\Entity\GlobalStatut;
use App\Entity\TicketStatut;
use App\Form\TicketStatutType;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers/{global}/ticket/statut", requirements={"global":"\d+"})
 */
class TicketStatutController extends AbstractController
{
    private $global;
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/", name="ticket_statut_index", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatut $global, DataTableFactory $dataTableFactory): Response
    {

        $this->global = $global;

        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('ticket_statut_show', 
                [
                    'id' => $value,
                    'global' => $this->session->get('global')
                ]
                ));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('ticket_statut_edit', 
                [
                    'id' => $value,
                    'global' => $this->session->get('global')
                ]
                ));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => TicketStatut::class,
            'query' => function(QueryBuilder $builder) {
                return $builder
                ->select('t')
                ->where('t.global_statut = :g')
                ->setParameter('g', $this->global)
                ->from(TicketStatut::class, 't');
            }
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('ticket_statut/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="ticket_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request, GlobalStatut $global): Response
    {
        $ticketStatut = new TicketStatut();
        $form = $this->createForm(TicketStatutType::class, $ticketStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketStatut->setGlobalStatut($global);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticketStatut);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_statut_index', [
                'global' => $this->session->get('global')
            ]);
        }

        return $this->render('ticket_statut/new.html.twig', [
            'ticket_statut' => $ticketStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_statut_show", methods={"GET"})
     */
    public function show(GlobalStatut $global, TicketStatut $ticketStatut): Response
    {
        if($ticketStatut->getGlobalStatut() === $global){
            return $this->render('ticket_statut/show.html.twig', [
                'ticket_statut' => $ticketStatut,
            ]);
        }
        $this->addFlash('error', 'Le statut ticket '.$ticketStatut->getId().' n\'a pas pour statut global '.$global->getId());
        return $this->redirectToRoute('ticket_statut_index', [
            'global' => $this->session->get('global')
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ticket_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GlobalStatut $global, TicketStatut $ticketStatut): Response
    {
        if($ticketStatut->getGlobalStatut() === $global){
            $form = $this->createForm(TicketStatutType::class, $ticketStatut);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('ticket_statut_index', [
                    'global' => $this->session->get('global')
                ]);
            }
    
            return $this->render('ticket_statut/edit.html.twig', [
                'ticket_statut' => $ticketStatut,
                'form' => $form->createView(),
            ]);
        }

        $this->addFlash('error', 'Le statut ticket '.$ticketStatut->getId().' n\'a pas pour statut global '.$global->getId());
        return $this->redirectToRoute('ticket_statut_index', [
            'global' => $this->session->get('global')
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GlobalStatut $global, TicketStatut $ticketStatut): Response
    {
        if($ticketStatut->getGlobalStatut() === $global){
            if ($this->isCsrfTokenValid('delete'.$ticketStatut->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($ticketStatut);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('ticket_statut_index');
        }

        $this->addFlash('error', 'Le statut ticket '.$ticketStatut->getId().' n\'a pas pour statut global '.$global->getId());
        return $this->redirectToRoute('ticket_statut_index', [
            'global' => $this->session->get('global')
        ]);
       
    }
}
