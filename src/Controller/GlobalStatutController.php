<?php

namespace App\Controller;

use App\Entity\GlobalStatut;
use App\Form\GlobalStatutType;
use App\Repository\GlobalStatutRepository;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FindByRoles;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/")
 */
class GlobalStatutController extends AbstractController
{

    private $findByRoles;

    public function __construct(SessionInterface $session, FindByRoles $findByRoles)
    {
        $this->findByRoles = $findByRoles;
    }

    /**
     * @Route("/", name="default", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatutRepository $globalStatutRepository, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Créer le', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $customers = sprintf('<a href="%s" class="btn btn-primary">Les fiches</a>', $this->generateUrl('customer_files_index', ['global' => $value]));
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('global_statut_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('global_statut_edit', ['id' => $value]));
                return $customers.$show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => GlobalStatut::class,
            'query' => function(QueryBuilder $builder) {
                if(!$this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
                    $user_global_statuts = $this->getUser()->getGlobalStatut();
                    if(count($user_global_statuts) > 0){
                        return $builder
                        ->select('c')
                        ->where('c IN(:g)')
                        ->setParameter('g', $user_global_statuts)
                        ->from(GlobalStatut::class, 'c');
                    }
                }

                return $builder
                ->select('c')
                ->from(GlobalStatut::class, 'c');
            }
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('global_statut/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/admin/global_statut/new", name="global_statut_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $globalStatut = new GlobalStatut();
        $form = $this->createForm(GlobalStatutType::class, $globalStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($globalStatut);
            $entityManager->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('global_statut/new.html.twig', [
            'global_statut' => $globalStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="global_statut_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(GlobalStatut $globalStatut): Response
    {
        return $this->render('global_statut/show.html.twig', [
            'global_statut' => $globalStatut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="global_statut_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GlobalStatut $globalStatut): Response
    {
        $form = $this->createForm(GlobalStatutType::class, $globalStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('global_statut/edit.html.twig', [
            'global_statut' => $globalStatut,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/global_statut/{id}", name="global_statut_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GlobalStatut $globalStatut): Response
    {
        if ($this->isCsrfTokenValid('delete'.$globalStatut->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($globalStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('global_statut_index');
    }

    public function displayStatut(Session $session, GlobalStatutRepository $globalStatutRepository){
        $name = null;

        if($session->get('global')){
            $name = $globalStatutRepository->find($session->get('global'))->getName();
        }

        return $this->render('global_statut/display.html.twig', [
            'global_statut' => $name
        ]);
    }
}
