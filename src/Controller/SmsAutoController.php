<?php

namespace App\Controller;

use App\Entity\GlobalStatut;
use App\Entity\SmsAuto;
use App\Form\SmsAutoType;
use App\Repository\SmsAutoRepository;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UpdateSmsAutoType;
use App\Repository\CustomerFilesRepository;
use Symfony\Component\Finder\Glob;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/customers/admin/{global}/sms/auto", requirements={"global":"\d+"})
 */
class SmsAutoController extends AbstractController
{

    private $session;
    private $global;

    public function __construct(SessionInterface $sessionInterface)
    {
        $this->session = $sessionInterface;
    }
    /**
     * @Route("/", name="sms_auto_index", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatut $global,DataTableFactory $dataTableFactory): Response
    {
        $this->global = $global;

        $table = $dataTableFactory->create()
            ->add('id', NumberColumn::class, ['label' => '#'])
            // ->add('step', TextColumn::class, ['label' => 'Etape'])
            ->add('content', TextColumn::class, ['label' => 'Message'])
            ->add('actions', TextColumn::class, [
                'data' => function($context) {
                    return $context->getId();
                }, 
                'render' => function($value, $context){
                    $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('sms_auto_show', [
                        'id' => $value,
                        'global' => $this->session->get('global')
                    ]));
                    $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('sms_auto_edit', [
                        'id' => $value,
                        'global' => $this->session->get('global')
                    ]));
                    return $show.$edit;
                }, 
                'label' => 'Actions'
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => SmsAuto::class,
                'query' => function(QueryBuilder $builder){
                    return $builder
                    ->select('s')
                    ->where('s.global_statut = :g')
                    ->setParameter('g', $this->global)
                    ->from(SmsAuto::class, 's');
                }
            ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('sms_auto/index.html.twig', [
            "datatable" => $table,
        ]);
    }

    /**
     * @Route("/new", name="sms_auto_new", methods={"GET","POST"})
     */
    public function new(Request $request, GlobalStatut $global, CustomerFilesRepository $customerFilesRepository): Response
    {
        $smsAuto = new SmsAuto();
        $form = $this->createForm(SmsAutoType::class, $smsAuto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $smsAuto->setGlobalStatut($global);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($smsAuto);
            $entityManager->flush();

            return $this->redirectToRoute('sms_auto_index', [
                'global' => $this->session->get('global')
            ]);
        }

        return $this->render('sms_auto/new.html.twig', [
            'sms_auto' => $smsAuto,
            'form' => $form->createView(),
            'fields' => $customerFilesRepository->getStepFields(),
            'complementsFields' => $customerFilesRepository->getComplementFields()
        ]);
    }

    /**
     * @Route("/{id}", name="sms_auto_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(GlobalStatut $global, SmsAuto $smsAuto): Response
    {

        return $this->render('sms_auto/show.html.twig', [
            'sms_auto' => $smsAuto,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sms_auto_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, GlobalStatut $global, SmsAuto $smsAuto, CustomerFilesRepository $customerFilesRepository): Response
    {
        $form = $this->createForm(SmsAutoType::class, $smsAuto);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sms_auto_index', [
                'global' => $this->session->get('global')
            ]);
        }

        return $this->render('sms_auto/edit.html.twig', [
            'sms_auto' => $smsAuto,
            'form' => $form->createView(),
            'fields' => $customerFilesRepository->getStepFields(),
            'complementsFields' => $customerFilesRepository->getComplementFields()
        ]);
    }

    /**
     * @Route("/{id}", name="sms_auto_delete", methods={"DELETE"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, GlobalStatut $global, SmsAuto $smsAuto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$smsAuto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($smsAuto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sms_auto_index', [
            'global' => $this->session->get('global')
        ]);
    }
}
