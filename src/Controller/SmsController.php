<?php

namespace App\Controller;

use App\Entity\Sms;
use App\Entity\SmsAuto;
use App\Form\SmsType;
use App\Repository\SmsRepository;
use App\Service\SendSms;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
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
 * @Route("/admin/sms")
 */
class SmsController extends AbstractController
{

    private $sms;

    public function __construct(SendSms $sendSms)
    {
        $this->sms = $sendSms;
    }

    /**
     * @Route("/", name="sms_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('receiver', TextColumn::class, ['label' => 'Numéro de téléphone'])
        ->add('message', TextColumn::class, ['label' => 'Message'])
        ->add('creationDatetime', DateTimeColumn::class, ['label' => 'Créer le', 'format' => 'd-m-Y H:i:s'])
        ->add('sentAt', DateTimeColumn::class, ['label' => 'Envoyer le', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context['id'];
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('sms_show', ['id' => $value]));
                return $show;
            }, 
            'label' => 'Actions'
        ])
        ->createAdapter(ArrayAdapter::class, $this->sms->getOutGoinsAsArray())
        ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('sms/index.html.twig', [
            'datatable' => $table
        ]);
    }


    /**
     * @Route("/new", name="sms_new", methods={"GET","POST"})
     */
    public function new(Request $request, SendSms $sendSms): Response
    {
        $sms = new Sms();
        $form = $this->createForm(SmsType::class, $sms);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = [];
            preg_match_all('/(?:(?:\+|00)33)\s*[67](?:[\s.-]*\d{2}){4}/', $sms->getPhoneNumber(), $matches, PREG_PATTERN_ORDER);

            foreach (explode(',', $sms->getPhoneNumber()) as $value) {
                if(!in_array($value, $matches[0])){
                    $errors[] = $value;
                }
            }

            if(!empty($errors)){
                $this->addFlash('error', 'Les numéros de téléphone suivant ne sont pas valides : '.implode(', ', $errors));
                return $this->render('sms/new.html.twig', ['phones' => $sms->getPhoneNumber(), 'message' => $sms->getContent(),             'form' => $form->createView()]);
            }

            // $sendSms->send($sms->getContent(), $matches[0]);
            $this->addFlash('success', 'Le message à bien été envoyer !');
            return $this->redirectToRoute('sms_index');
        }

        return $this->render('sms/new.html.twig', [
            'sms' => $sms,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/outgoing", name="sms_show", methods={"GET"})
     */
    public function show(Request $request): Response
    {
        $sms = $request->query->get('id');
       
        return $this->render('sms/show.html.twig', [
            'sms' => $this->sms->getOutGoing($sms),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sms_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sms $sms): Response
    {
        $form = $this->createForm(SmsType::class, $sms);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sms_index');
        }

        return $this->render('sms/edit.html.twig', [
            'sms' => $sms,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="sms_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $sms = $request->query->get('id');

        if ($this->isCsrfTokenValid('delete'.$sms, $request->request->get('_token'))) {
            $this->sms->deleteOutGoing($sms);
        }
        $this->addFlash('success', "SMS numéro $sms à bien été supprimer !");
        return $this->redirectToRoute('sms_index');
    }
}
