<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\Provider;
use App\Entity\ProviderProduct;
use App\Form\CustomerFilesType;
use App\Repository\CustomerFilesRepository;
use App\Repository\ProviderProductRepository;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @Route("/")
 */
class CustomerFilesController extends AbstractController
{
    /**
     * @Route("/", name="default", methods={"GET"})
     */
    public function index(CustomerFilesRepository $customerFilesRepository): Response
    {        
        
        return $this->render('customer_files/index.html.twig', [
            'customer_files' => $customerFilesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customer_files_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProviderRepository $provider): Response
    {
        $customerFile = new CustomerFiles();
        $form = $this->createForm(CustomerFilesType::class, $customerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFile);
            $entityManager->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('customer_files/new.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
            'providers' => $provider->findAll()
        ]);
    }

    /**
     * @Route("/getProviderParams/{id}", name="customer_files_get_provider_params", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function getProviderParams(Request $request, Provider $provider): object {
        $parameters = [];
        foreach($provider->getProviderProducts() as $value){
            foreach($value->getParams() as $param){
                $parameters[$param->getId()] = $param->getName();
            }
        }
        return new JsonResponse(array_unique($parameters));
    }

    /**
     * @Route("/getProviderProducts/{id}", name="customer_files_products", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function getProductsProvider(Request $request, Provider $provider, ProviderProductRepository $product): object {
        $content = json_decode($request->getContent(), true)['data'];
        $products = [];
        foreach($product->findByProductParam($content, $provider->getId()) as $value){
            $products[$value['id']] = $value['name'];
        }
        return new JsonResponse($products);
    }


    /**
     * @Route("/{id}", name="customer_files_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(CustomerFiles $customerFile): Response
    {
        return $this->render('customer_files/show.html.twig', [
            'customer_file' => $customerFile,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customer_files_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomerFiles $customerFile): Response
    {
        $form = $this->createForm(CustomerFilesType::class, $customerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('customer_files/edit.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customer_files_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomerFiles $customerFile): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerFile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customerFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('default');
    }
}
