<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\CustomerFilesRepository;
use App\Repository\ProviderProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class Controller extends AbstractController
{
    /**
     * @Route("/api/customers", name="api_customers", methods={"POST"})
    */
    public function getCustomers(Request $request, CustomerFilesRepository $repository): object {
        
        return new JsonResponse($repository->getPhones());
    }

   /**
     * @Route("/getProviderParams/{id}", name="customer_files_get_provider_params", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function getProviderParams(Request $request, Provider $provider, CustomerFilesRepository $repository): object {
        $result = [];
        foreach ($repository->getProviderParams($provider) as $key => $value) {
            $result[$value->getId()] = $value->getName();
        }
        return new JsonResponse($result);
    }

    /**
     * @Route("/getProviderProducts/{id}", name="customer_files_products", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function getProductsProvider(Request $request, Provider $provider, ProviderProductRepository $productsRepository) {
        $params = json_decode($request->getContent(), true)['data'];
        $products = $provider->getProviderProducts();
        $productParams = [];
        $result = [];
        foreach($products as $product){
            foreach($product->getParams() as $param){
                $productParams[$product->getId()][] = $param->getId();
            }
            if(isset($productParams[$product->getId()])){
                if(array_diff($productParams[$product->getId()], $params) === array_diff($params, $productParams[$product->getId()])){
                    $result[] = ['id' => $product->getId(), "name" => $product->getName()];
                }
            }
        }
        return new JsonResponse($result);
    }
}
