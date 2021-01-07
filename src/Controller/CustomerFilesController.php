<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\Notification;
use App\Entity\Provider;
use App\Entity\ProviderProduct;
use App\Form\CustomerFilesType;
use App\Form\UpdateCustomerFileType;
use App\Form\UpdateCustomerMailType;
use App\Form\UpdateCustomerPasswordType;
use App\Repository\CustomerFilesRepository;
use App\Repository\ProviderProductRepository;
use App\Repository\ProviderRepository;
use App\Service\Mailer;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $customer_files = $customerFilesRepository->findAll();
        if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
            $customer_files = $customerFilesRepository->getInstaller($this->getUser());
        }

        return $this->render('customer_files/index.html.twig', [
            'customer_files' => $customer_files,
        ]);
    }


    /**
     * @IsGranted("ROLE_ALLOW_CREATE")
     * @Route("/new", name="customer_files_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProviderRepository $provider, NotificationService $notificationService, Mailer $mailer): Response
    {
        $customerFile = new CustomerFiles();
        $form = $this->createForm(CustomerFilesType::class, $customerFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFile);
            $entityManager->flush();
            if($customerFile->getInstaller()){
                // Send notification
                $notificationService->sendNotification([$customerFile->getInstaller()], 'Une nouvelle fiche vous à été attribuer !', "/".$customerFile->getId());
                // // Send mail
                $mailer->sendMail([$customerFile->getInstaller()], 'Nouveau ticket Lergon\'Home', 'customer_files/email_template/installer.html.twig', ['customer' => $customerFile]);
            }

            $this->addFlash('success', 'La fiche a bien été enregistrée !');
            return $this->redirectToRoute('customer_files_show', ['id' => $customerFile->id]);

        }

        return $this->render('customer_files/new.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
            'providers' => $provider->findAll()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
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
     * @IsGranted("ROLE_USER")
     * @Route("/getProviderProducts/{id}", name="customer_files_products", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function getProductsProvider(Request $request, Provider $provider, ProviderProductRepository $productsRepository): object {
        $params = json_decode($request->getContent(), true)['data'];
        $products = $productsRepository->findBy(['provider' => $provider]);
        $productParams = [];
        $result = [];
        foreach($products as $product){
            foreach($product->getParams() as $param){
                $productParams[$product->getId()][] = $param->getId();
            }
            if(array_diff($productParams[$product->getId()], $params) === array_diff($params, $productParams[$product->getId()])){
                $result[$product->getId()] = $product->getName();
            }
        }
        return new JsonResponse($result);
    }


    /**
     * @Route("/{id}", name="customer_files_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(CustomerFiles $customerFile): Response
    {
        if(!in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles()) || $customerFile->getInstaller() === $this->getUser()){
            return $this->render('customer_files/show.html.twig', [
                'customer_file' => $customerFile,
            ]);
        }
        $this->addFlash('error', 'Vous n\'avez pas accès à cette fiche ');
        return $this->redirectToRoute('default');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/edit", name="customer_files_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomerFiles $customerFile, ProviderRepository $provider, CustomerFilesRepository $repository): Response
    {
        $form = $this->createForm(UpdateCustomerFileType::class, $customerFile);
        $form->handleRequest($request);

        $password = $this->createForm(UpdateCustomerPasswordType::class, $customerFile);
        $password->handleRequest($request);

        $mail = $this->createForm(UpdateCustomerMailType::class, $customerFile);
        $mail->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La fiche à bien été modifiée !');
            return $this->redirectToRoute('customer_files_edit', ['id' => $customerFile->id]);

        }else if($password->isSubmitted() && $password->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le mot de passe de la fiche à bien été modifié !');
            return $this->redirectToRoute('customer_files_edit', ['id' => $customerFile->id]);

        }else if($mail->isSubmitted() && $mail->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'L\'email de la fiche à bien été modifié !');
            return $this->redirectToRoute('customer_files_edit', ['id' => $customerFile->id]);
        }

        return $this->render('customer_files/edit.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
            'mailForm' => $mail->createView(),
            'pwdForm' => $password->createView(),
            'providers' => $provider->findAll(),
            'params' => $repository->getProviderParams($customerFile->getProduct() ? $customerFile->getProduct()->getProvider() : null)
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
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
