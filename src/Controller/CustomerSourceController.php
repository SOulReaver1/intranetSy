<?php

namespace App\Controller;

use App\Entity\CustomerSource;
use App\Form\CustomerSourceType;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/source")
 */
class CustomerSourceController extends AbstractController
{
    /**
     * @Route("/", name="customer_source_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
        ->add('id', TextColumn::class, ['label' => 'id'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Date de création', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('customer_source_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('customer_source_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => CustomerSource::class
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('customer_source/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="customer_source_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $customerSource = new CustomerSource();
        $form = $this->createForm(CustomerSourceType::class, $customerSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerSource);
            $entityManager->flush();
            $this->addFlash('success', 'Votre source à bien été créer !');
            return $this->redirectToRoute('customer_source_index');
        }

        return $this->render('customer_source/new.html.twig', [
            'customer_source' => $customerSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_source_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(CustomerSource $customerSource): Response
    {
        return $this->render('customer_source/show.html.twig', [
            'customer_source' => $customerSource,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_source_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomerSource $customerSource): Response
    {
        $form = $this->createForm(CustomerSourceType::class, $customerSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre source à bien été modifier !');
            return $this->redirectToRoute('customer_source_index');
        }

        return $this->render('customer_source/edit.html.twig', [
            'customer_source' => $customerSource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_source_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomerSource $customerSource): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerSource->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerSource);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Votre source à bien été supprimer !');
        return $this->redirectToRoute('customer_source_index');
    }
}
