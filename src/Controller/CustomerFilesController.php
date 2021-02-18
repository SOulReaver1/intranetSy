<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\CustomerFilesStatut;
use App\Entity\Notification;
use App\Entity\Provider;
use App\Entity\ProviderProduct;
use App\Form\CustomerFilesType;
use App\Form\UpdateCustomerFileType;
use App\Form\UpdateCustomerMailType;
use App\Form\UpdateCustomerPasswordType;
use App\Repository\CustomerFilesRepository;
use App\Repository\CustomerFilesStatutRepository;
use App\Repository\ProviderProductRepository;
use App\Repository\ProviderRepository;
use App\Service\Mailer;
use App\Service\NotificationService;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\Event\ORMAdapterQueryEvent;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapterEvents;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/")
 */
class CustomerFilesController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $sessionInterface)
    {
        $this->session = $sessionInterface;
    }
    /**
     * @Route("/", name="default", methods={"GET", "POST"})
     */
    public function index(Request $request, CustomerFilesStatutRepository $customerFilesStatutRepository, DataTableFactory $dataTableFactory)
    {        
        if($request->isMethod('get')){
            $this->session->remove('statut');
            if($request->query->get('statut')){
                $this->session->set('statut', $request->query->get('statut'));
            }
        }

        $table = $dataTableFactory->create()
            ->setTemplate('@DataTables/datatable_html.html.twig', ['className' => 'table table-striped'])
            ->add('id', TextColumn::class, ['label' => '#'])
            ->add('customer_statut', TextColumn::class, [
                'field' => 'customer_statut.name', 
                'label' => 'Statut dossier'
            ])
            ->add('name', TextColumn::class, ['label' => 'Nom complet'])
            ->add('date_expertise', DateTimeColumn::class, ['label' => 'Date d\'expertise'])
            ->add('address', TextColumn::class, ['label' => 'Adresse'])
            ->add('city', TextColumn::class, ['label' => 'Ville'])
            ->add('zip_code', TextColumn::class, ['label' => 'Code postal'])
            ->add('cellphone', TextColumn::class, ['label' => 'Téléphone portable'])
            ->add('home_phone', TextColumn::class, ['label' => 'Téléphone fixe'])
            ->add('mail_al', TextColumn::class, ['label' => 'Mail AL'])
            ->add('password_al', TextColumn::class, ['label' => 'Mot de passe AL'])
            ->add('actions', TextColumn::class, [
                'data' => function($context) {
                    return $context->getId();
                }, 
                'render' => function($value, $context){
                    $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('customer_files_show', ['id' => $value]));
                    $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('customer_files_edit', ['id' => $value]));
                    return $show.$edit;
                }, 
                'label' => 'Actions'
            ])
            ->addOrderBy('id', DataTable::SORT_ASCENDING)
            ->createAdapter(ORMAdapter::class, [
                'entity' => CustomerFiles::class,
                'query' => function (QueryBuilder $builder) {
                    if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
                        if($this->session->get('statut')){
                            return $builder
                            ->select('c, customer_statut')
                            ->where('i = :i')
                            ->where('customer_statut.id = :statut')
                            ->setParameter("statut", $this->session->get('statut'))
                            ->setParameter('i', $this->getUser())
                            ->from(CustomerFiles::class, 'c')
                            ->leftJoin('c.installer', 'i')
                            ->leftJoin('e.customer_statut', 'customer_statut');
                        }
                        return $builder
                        ->select('c, customer_statut')
                        ->where('i = :i')
                        ->setParameter('i', $this->getUser())
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.installer', 'i')
                        ->leftJoin('c.customer_statut', 'customer_statut');

                    }
                    if($this->session->get('statut')){
                        return $builder
                        ->select('e, customer_statut')
                        ->where('customer_statut.id = :statut')
                        ->setParameter("statut", $this->session->get('statut'))
                        ->from(CustomerFiles::class, 'e')
                        ->leftJoin('e.customer_statut', 'customer_statut')
                        ;
                    }
                    return $builder
                        ->select('e, customer_statut')
                        ->from(CustomerFiles::class, 'e')
                        ->leftJoin('e.customer_statut', 'customer_statut')
                    ;
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('customer_files/index.html.twig', [
            'statuts' => $customerFilesStatutRepository->findAllByOrder(),
            'datatable' => $table
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
            return $this->redirectToRoute('customer_files_show', ['id' => $customerFile->getId()]);

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
     * @Route("/{id}/commentary", name="customer_file_change_commentary", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function changeCommentary(Request $request, CustomerFiles $customerFile): object {
        $data = json_decode($request->getContent(), true);
        $commentary = $data['commentary'] ?? null;
        if($commentary){
            $customerFile->setCommentary($commentary);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['status' => 200]);

        }
        return new JsonResponse(['status' => 404]);

    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/api/customers", name="api_customers", methods={"POST"})
    */
    public function getCustomers(Request $request, CustomerFilesRepository $repository): object {
        
        return new JsonResponse($repository->getCustomers());
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
    public function show(Request $request, CustomerFiles $customerFile): Response
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
            return $this->redirectToRoute('customer_files_show', ['id' => $customerFile->getId()]);

        }else if($password->isSubmitted() && $password->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le mot de passe de la fiche à bien été modifié !');
            return $this->redirectToRoute('customer_files_show', ['id' => $customerFile->getId()]);

        }else if($mail->isSubmitted() && $mail->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'L\'email de la fiche à bien été modifié !');
            return $this->redirectToRoute('customer_files_show', ['id' => $customerFile->getId()]);
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
