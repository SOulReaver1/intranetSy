<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Form\AlertType;
use App\Repository\AlertRepository;
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

/**
 * @Route("/dev/alert")
 */
class AlertController extends AbstractController
{
    /**
     * @Route("/", name="alert_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('visible', TextColumn::class, [
            'render' => function($value){
                $class = $value ? 'badge-success' : 'badge-danger';
                $value = $value ? 'Oui' : 'Non';
                return sprintf("<span class='badge $class'>$value</span>");
            },
            'label' => 'Visible'
        ])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Créer le', 'format' => 'd-m-Y'])
        ->add('updated_at', DateTimeColumn::class, ['label' => 'Modifier le', 'format' => 'd-m-Y'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('alert_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('alert_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])
        ->addOrderBy('id', DataTable::SORT_DESCENDING)
        ->createAdapter(ORMAdapter::class, [
            'entity' => Alert::class,
        ])->handleRequest($request);

        if($table->isCallback()){
            return $table->getResponse();
        }

        return $this->render('alert/index.html.twig', [
            'datatable' => $table
        ]);
    }

    public function getLastAlert(AlertRepository $alertRepository){
        $lastAlert = $alertRepository->findBy(['visible' => true], ['id' => 'DESC']);
        return $this->render('alert/display.html.twig', [
            'alert' => $lastAlert ? $lastAlert[0] : null
        ]);
    }

    /**
     * @Route("/new", name="alert_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $alert = new Alert();
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($alert);
            $entityManager->flush();

            return $this->redirectToRoute('alert_index');
        }

        return $this->render('alert/new.html.twig', [
            'alert' => $alert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="alert_show", methods={"GET"})
     */
    public function show(Alert $alert): Response
    {
        return $this->render('alert/show.html.twig', [
            'alert' => $alert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="alert_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Alert $alert): Response
    {
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('alert_index');
        }

        return $this->render('alert/edit.html.twig', [
            'alert' => $alert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="alert_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Alert $alert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($alert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('alert_index');
    }
}
