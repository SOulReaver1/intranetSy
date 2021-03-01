<?php

namespace App\Controller;

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

/**
 * @Route("/admin/sms/auto")
 */
class SmsAutoController extends AbstractController
{
    /**
     * @Route("/", name="sms_auto_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        
        $table = $dataTableFactory->create()
            ->add('id', NumberColumn::class, ['label' => '#'])
            ->add('step', TextColumn::class, ['label' => 'Etape'])
            ->add('content', TextColumn::class, ['label' => 'Message'])
            ->add('actions', TextColumn::class, [
                'data' => function($context) {
                    return $context->getId();
                }, 
                'render' => function($value, $context){
                    $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('sms_auto_show', ['id' => $value]));
                    $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('sms_auto_edit', ['id' => $value]));
                    return $show.$edit;
                }, 
                'label' => 'Actions'
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => SmsAuto::class,
            ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('sms/auto.html.twig', [
            "datatable" => $table,
        ]);
    }

    /**
     * @Route("/new", name="sms_auto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $smsAuto = new SmsAuto();
        $form = $this->createForm(SmsAutoType::class, $smsAuto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($smsAuto);
            $entityManager->flush();

            return $this->redirectToRoute('sms_auto_index');
        }

        return $this->render('sms_auto/new.html.twig', [
            'sms_auto' => $smsAuto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sms_auto_show", methods={"GET"})
     */
    public function show(SmsAuto $smsAuto): Response
    {

        return $this->render('sms_auto/show.html.twig', [
            'sms_auto' => $smsAuto,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sms_auto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SmsAuto $smsAuto): Response
    {
        $form = $this->createForm(UpdateSmsAutoType::class, $smsAuto);
        $form->handleRequest($request);

        $step = $smsAuto->getStep();
        if($step === 1){
            $documentsAllowed = ["name", "documents"];
        }else if($step === 2){
            $documentsAllowed = ["name", "documents", "dateMetrage"];
        }else if($step === 3){
            $documentsAllowed = ["name", "documents", "dateMetrage"];
        }else if($step === 4){
            $documentsAllowed = ["name", "documents", "dateMetrage"];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sms_auto_index');
        }

        return $this->render('sms_auto/edit.html.twig', [
            'sms_auto' => $smsAuto,
            'form' => $form->createView(),
            'documents' => $documentsAllowed
        ]);
    }

    /**
     * @Route("/{id}", name="sms_auto_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SmsAuto $smsAuto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$smsAuto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($smsAuto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sms_auto_index');
    }
}
