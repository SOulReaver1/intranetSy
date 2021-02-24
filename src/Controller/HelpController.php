<?php

namespace App\Controller;

use App\Entity\Help;
use App\Form\HelpType;
use App\Service\FindByRoles;
use App\Service\Mailer;
use App\Service\NotificationService;
use DateTime;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{

    /**
     * @Route("/dev/help/index", name="help_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('statut', TextColumn::class, ['label' => 'Statut', 'field' => 'statut.name'])
        ->add('title', TextColumn::class, ['label' => 'Titre'])
        ->add('description', TextColumn::class, ['label' => 'Description'])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Date de création', 'format' => 'd-m-Y H:i:s'])
        ->addOrderBy('created_at',  DataTable::SORT_DESCENDING)
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('help_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('help_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => Help::class
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('help/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/help/new", name="help_new", methods={"GET","POST"})
     */
    public function new(Request $request, NotificationService $notificationService, Mailer $mailer, FindByRoles $findByRoles): Response
    {
        $help = new Help();
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $help->setUserId($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($help);
            $entityManager->flush();
            // Send notification
            $notificationService->sendNotification($findByRoles->findByRole('ROLE_DEVELOPER'), 'Un bug à été signalé !', "/dev/help/".$help->getId());
            // Send mail
            $mailer->sendMail($findByRoles->findByRole('ROLE_DEVELOPER'), 'Nouvelle demande d\'aide Intranet Lergon\'Home', 'help/_email.html.twig', ['help' => $help]);
            $this->addFlash('success', 'Merci ! Votre bug à bien été signalé !');  
            return $this->redirectToRoute('default');
        }

        return $this->render('help/new.html.twig', [
            'help' => $help,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dev/help/{id}", name="help_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Help $help): Response
    {
        if(!$help->getReadAt()){
            $help->setReadAt(new DateTime('now'));
        }
        return $this->render('help/show.html.twig', [
            'help' => $help,
        ]);
    }

    /**
     * @Route("/dev/help/{id}/edit", name="help_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Help $help): Response
    {
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('help_index');
        }

        return $this->render('help/edit.html.twig', [
            'help' => $help,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dev/help/{id}", name="help_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Help $help): Response
    {
        if ($this->isCsrfTokenValid('delete'.$help->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($help);
            $entityManager->flush();
        }

        return $this->redirectToRoute('help_index');
    }
}
