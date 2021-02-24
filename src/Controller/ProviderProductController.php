<?php

namespace App\Controller;

use App\Entity\ProviderProduct;
use App\Form\ProviderProductType;
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
 * @Route("/admin/provider/product")
 */
class ProviderProductController extends AbstractController
{
    /**
     * @Route("/", name="provider_product_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        
        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('name', TextColumn::class, ['label' => 'Nom'])
        ->add('provider', TextColumn::class, ['label' => 'Fournisseur', 'field' => 'provider.name'])
        ->add('params', TextColumn::class, [
            'data' => function($context){
                $params = array();
                foreach ($context->getParams() as $value) {
                   $params[] = $value->getName();
                }
                return implode(', ', $params);
            },
            'label' => 'Les paramètres'
        ])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Créer le', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('provider_product_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('provider_product_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])->createAdapter(ORMAdapter::class, [
            'entity' => ProviderProduct::class,
            'query' => function(QueryBuilder $builder){
                return $builder
                ->select('c, provider')
                ->from(ProviderProduct::class, 'c')
                ->leftJoin('c.provider', 'provider');
            }
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('provider_product/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/new", name="provider_product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $providerProduct = new ProviderProduct();
        $form = $this->createForm(ProviderProductType::class, $providerProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($providerProduct);
            $entityManager->flush();

            return $this->redirectToRoute('provider_product_index');
        }

        return $this->render('provider_product/new.html.twig', [
            'provider_product' => $providerProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_product_show", methods={"GET"})
     */
    public function show(ProviderProduct $providerProduct): Response
    {
        return $this->render('provider_product/show.html.twig', [
            'provider_product' => $providerProduct,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="provider_product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProviderProduct $providerProduct): Response
    {
        $form = $this->createForm(ProviderProductType::class, $providerProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('provider_product_index');
        }

        return $this->render('provider_product/edit.html.twig', [
            'provider_product' => $providerProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provider_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProviderProduct $providerProduct): Response
    {
        if ($this->isCsrfTokenValid('delete'.$providerProduct->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($providerProduct);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provider_product_index');
    }
}
