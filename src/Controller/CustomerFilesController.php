<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\GlobalStatut;
use App\Entity\Provider;
use App\Entity\User;
use App\Form\CustomerFilesType;
use App\Form\StatusTransferType;
use App\Form\UpdateCustomerFileType;
use App\Form\UpdateCustomerGlobalStatutType;
use App\Form\UpdateCustomerMailType;
use App\Form\UpdateCustomerPasswordType;
use App\Repository\ClientStatutDocumentRepository;
use App\Repository\CustomerFilesRepository;
use App\Repository\CustomerFilesStatutRepository;
use App\Repository\ProviderProductRepository;
use App\Repository\ProviderRepository;
use App\Repository\SmsAutoRepository;
use App\Service\FindByRoles;
use App\Service\Mailer;
use App\Service\NotificationService;
use App\Service\SendSms;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
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
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Finder\Glob;

/**
 * @Route("/customers/{global}", requirements={"global":"\d+"})
 */
class CustomerFilesController extends AbstractController
{

    private $session;
    private $smsAutoRepository;
    private $sendSms;
    private $clientStatutDocumentRepository;
    private $globalStatut;
    private $findByRoles;
    private $customerFilesRepository;
    private $zip_code;

    public function __construct(FindByRoles $findByRoles,SessionInterface $sessionInterface, SmsAutoRepository $smsAutoRepository, SendSms $sendSms, ClientStatutDocumentRepository $clientStatutDocumentRepository, CustomerFilesRepository $customerFilesRepository)
    {
        $this->session = $sessionInterface;
        $this->smsAutoRepository = $smsAutoRepository;
        $this->sendSms = $sendSms;
        $this->clientStatutDocumentRepository = $clientStatutDocumentRepository;
        $this->findByRoles = $findByRoles;
        $this->customerFilesRepository = $customerFilesRepository;
    }
    /**
     * @Route("/", name="customer_files_index", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatut $global, CustomerFilesStatutRepository $customerFilesStatutRepository,
    CustomerFilesRepository $customerFilesRepository,DataTableFactory $dataTableFactory)
    {        
        $this->globalStatut = $global;
        $editStatutForm = $this->createForm(StatusTransferType::class, [
            "global" => $global
        ]);
        $departmentList = [];

        foreach($customerFilesRepository->getUniqueDepartment() as $value) {
            $departmentList[] = $value['department'];
        };

        $editStatutForm->handleRequest($request);
        if ($editStatutForm->isSubmitted() && $editStatutForm->isValid()) {
            $data = $editStatutForm->getData();
            $customer_files_ids = explode(', ', $data['customer_files']);
            $statut = $data['customer_files_statut'];
            $entityManager = $this->getDoctrine()->getManager();
            if($data['customer_files']) {
                foreach ($customer_files_ids as $value) {
                    $customerFile = $customerFilesRepository->find(intval($value));
                    // dd($value, $customerFile);
                    $customerFile->setCustomerStatut($statut);
                    $entityManager->persist($customerFile);
                    $entityManager->flush();    
                }
                $this->addFlash('success', count($customer_files_ids).' fiche(s) on(t) bien été(s) modifiée(s) !');
            }else {
                $this->addFlash('error', 'Aucune fiche sélectionnée');
            }
            

            return $this->redirectToRoute('customer_files_index', [
                'global' => $this->session->get('global')
            ]);
        }

        if($request->isMethod('get')){
            $this->session->remove('statut');
            $this->session->remove('zipcode');
            $this->session->remove('departments');
            if($request->query->get('statut')){
                $this->session->set('statut', $request->query->get('statut'));
            }
            if($request->query->get('departments')){
                $this->session->set('departments', $request->query->get('departments'));
            }

            if(!$this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
                $user_global_statuts = $this->getUser()->getGlobalStatut();
                if(count($user_global_statuts) > 0){
                    if(in_array($this->globalStatut, $user_global_statuts->toArray())){
                        $this->session->set('global', $this->globalStatut->getId());
                    } else {
                        $this->addFlash('error', "Vous n'avez pas accès au statut global : ".$this->globalStatut->getName());
                        return $this->redirectToRoute('default');
                    }
                } else {
                    $this->session->set('global', $this->globalStatut->getId());
                }
            } else {
                $this->session->set('global', $this->globalStatut->getId());
            }
        }

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => '#'])
            ->add("Select", TextColumn::class, ['label' => 'Select', 'render' => function() {
                return null;
            }])
            ->add('global_statut', TextColumn::class, [
                'data' => function($context) {
                    return $context->getGlobalStatut();
                }, 
                'label' => 'Statut global'
            ])
            ->add('customer_statut', TextColumn::class, [
                'field' => 'customer_statut.name', 
                'label' => 'Statut dossier'
            ])
            ->add('name', TextColumn::class, ['label' => 'Nom complet'])
            ->add('date_expertise', DateTimeColumn::class, ['label' => 'Date d\'expertise', 'searchable' => false])
            ->add('address', TextColumn::class, ['label' => 'Adresse'])
            ->add('state', TextColumn::class, ['label' => 'Région'])
            ->add('address_complement', TextColumn::class, ['label' => 'Complément d\'adresse'])
            ->add('city', TextColumn::class, ['label' => 'Ville'])
            ->add('zip_code', TextColumn::class, ['label' => 'Code postal'])
            ->add('cellphone', TextColumn::class, ['label' => 'Téléphone portable'])
            ->add('home_phone', TextColumn::class, ['label' => 'Téléphone fixe'])
            ->add('mail_al', TextColumn::class, ['label' => 'Mail AL'])
            ->add('password_al', TextColumn::class, ['label' => 'Mot de passe AL'])
            ->add('commentary', TextColumn::class, ['label' => 'Commentaire'])
            ->add('actions', TextColumn::class, [
                'data' => function($context) {
                    return $context->getId();
                }, 
                'render' => function($value, $context){
                    $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('customer_files_show', ['global' => $this->globalStatut->getId(), 'id' => $value]));
                    $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('customer_files_edit', ['global' => $this->globalStatut->getId(), 'id' => $value]));
                    return $show.$edit;
                }, 
                'label' => 'Actions'
            ])
            ->addOrderBy('id', DataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, [
                'entity' => CustomerFiles::class,
                'query' => function (QueryBuilder $builder) {
                    if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
                        if($this->session->get('statut')){
                            return $builder
                            ->select('c, customer_statut')
                            ->andWhere('i = :i')
                            ->andWhere('customer_statut.id = :statut')
                            ->setParameter("statut", $this->session->get('statut'))
                            ->setParameter('i', $this->getUser())
                            ->andWhere('c.global_statut = :g')
                            ->setParameter("g", $this->globalStatut)
                            ->from(CustomerFiles::class, 'c')
                            ->leftJoin('c.installer', 'i')
                            ->leftJoin('c.customer_statut', 'customer_statut');                        
                        }
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('i = :i')
                        ->setParameter('i', $this->getUser())
                        ->andWhere('c.global_statut = :g')
                        ->setParameter("g", $this->globalStatut)
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.installer', 'i')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    if($this->session->get('departments') && $this->session->get('statut')){
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('c.global_statut = :g')
                        ->andWhere('c.department IN(:departments)')
                        ->andWhere('customer_statut.id = :statut')
                        ->setParameter("g", $this->globalStatut)
                        ->setParameter("departments", explode(',', $this->session->get('departments')))
                        ->setParameter("statut", $this->session->get('statut'))
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    if($this->session->get('departments')){
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('c.global_statut = :g')
                        ->andWhere('c.department IN(:departments)')
                        ->setParameter("g", $this->globalStatut)
                        ->setParameter("departments", explode(',', $this->session->get('departments')))
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    if($this->session->get('statut')){
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('c.global_statut = :g')
                        ->andWhere('customer_statut.id = :statut')
                        ->setParameter("g", $this->globalStatut)
                        ->setParameter("statut", $this->session->get('statut'))
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    return $builder
                        ->select('c, customer_statut')
                        ->where('c.global_statut = :g')
                        ->setParameter("g", $this->globalStatut)
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut')
                    ;
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('customer_files/index.html.twig', [
            'statuts' => $customerFilesStatutRepository->findAllByOrder($global),
            'datatable' => $table,
            "datatableSelect" => true,
            "editStatutForm" => $editStatutForm->createView(),
            'departmentList' => $departmentList
        ]);
    }

    /**
     * @Route("/all", name="customer_files_all", methods={"GET", "POST"})
     */
    public function all(Request $request, GlobalStatut $global,DataTableFactory $dataTableFactory, CustomerFilesStatutRepository $customerFilesStatutRepository) {
        $this->globalStatut = $global;

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => '#'])
            ->add('global_statut', TextColumn::class, [
                'data' => function($context) {
                    return $context->getGlobalStatut();
                }, 
                'label' => 'Statut global'
            ])
            ->add('customer_statut', TextColumn::class, [
                'field' => 'customer_statut.name', 
                'label' => 'Statut dossier'
            ])
            ->add('name', TextColumn::class, ['label' => 'Nom complet'])
            ->add('date_expertise', DateTimeColumn::class, ['label' => 'Date d\'expertise', 'globalSearchable' => false])
            ->add('address', TextColumn::class, ['label' => 'Adresse'])
            ->add('address_complement', TextColumn::class, ['label' => 'Complément d\'adresse'])
            ->add('city', TextColumn::class, ['label' => 'Ville'])
            ->add('zip_code', TextColumn::class, ['label' => 'Code postal'])
            ->add('cellphone', TextColumn::class, ['label' => 'Téléphone portable'])
            ->add('home_phone', TextColumn::class, ['label' => 'Téléphone fixe'])
            ->add('mail_al', TextColumn::class, ['label' => 'Mail AL'])
            ->add('password_al', TextColumn::class, ['label' => 'Mot de passe AL'])
            ->add('commentary', TextColumn::class, ['label' => 'Commentaire'])
            ->add('actions', TextColumn::class, [
                'data' => function($context) {
                    return $context->getId();
                }, 
                'render' => function($value, $context){
                    $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('customer_files_show', ['global' => $this->globalStatut->getId(), 'id' => $value]));
                    $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('customer_files_edit', ['global' => $this->globalStatut->getId(), 'id' => $value]));
                    return $show.$edit;
                }, 
                'label' => 'Actions'
            ])
            ->addOrderBy('id', DataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, [
                'entity' => CustomerFiles::class,
                'query' => function (QueryBuilder $builder) {
                    if(in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles())){
                        if($this->session->get('statut')){
                            return $builder
                            ->select('c, customer_statut')
                            ->andWhere('i = :i')
                            ->andWhere('customer_statut.id = :statut')
                            ->setParameter("statut", $this->session->get('statut'))
                            ->setParameter('i', $this->getUser())
                            ->from(CustomerFiles::class, 'c')
                            ->leftJoin('c.installer', 'i')
                            ->leftJoin('c.customer_statut', 'customer_statut');                        
                        }
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('i = :i')
                        ->setParameter('i', $this->getUser())
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.installer', 'i')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    if($this->session->get('statut')){
                        return $builder
                        ->select('c, customer_statut')
                        ->andWhere('customer_statut.id = :statut')
                        ->setParameter("statut", $this->session->get('statut'))
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut');
                    }
                    if(!$this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
                        $user_global_statuts = $this->getUser()->getGlobalStatut();
                        if(count($user_global_statuts) > 0){
                            return $builder
                            ->select('c, customer_statut')
                            ->where('c.global_statut in(:g)')
                            ->setParameter('g', $user_global_statuts)
                            ->from(CustomerFiles::class, 'c')
                            ->leftJoin('c.customer_statut', 'customer_statut');
                        }
                    }
                    return $builder
                        ->select('c, customer_statut')
                        ->from(CustomerFiles::class, 'c')
                        ->leftJoin('c.customer_statut', 'customer_statut')
                    ;
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('customer_files/all.html.twig', [
            'statuts' => $customerFilesStatutRepository->findAllByOrderWG(),
            'datatable' => $table        
        ]);
    }

    /**
     * @IsGranted("ROLE_ALLOW_CREATE")
     * @Route("/new", name="customer_files_new", methods={"GET","POST"})
     */
    public function new(Request $request, GlobalStatut $global,ProviderRepository $provider, NotificationService $notificationService, Mailer $mailer): Response
    {
        $customerFile = new CustomerFiles();
        $form = $this->createForm(CustomerFilesType::class, $customerFile, array('global' => $global));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerFile->setCreatedBy($this->getUser());
            $customerFile->setGlobalStatut($global);
            if($customerFile->getInstaller()){
                // Send notification
                $notificationService->sendNotification([$customerFile->getInstaller()], 'Une nouvelle fiche vous à été attribuer !', "/".$customerFile->getId());
                // // Send mail
                $mailer->sendMail([$customerFile->getInstaller()], 'Nouveau ticket Lergon\'Home', 'customer_files/email_template/installer.html.twig', ['customer' => $customerFile]);
            }
            // $this->sendSmsToCustomerFile($customerFile, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFile);
            $entityManager->flush();
            $this->smsAutoRepository->checkSms($customerFile);
            $this->addFlash('success', 'La fiche a bien été enregistrée !');
            return $this->redirectToRoute('customer_files_show', [
                'id' => $customerFile->getId(),
                'global' => $this->session->get('global')
            ]);

        }

        return $this->render('customer_files/new.html.twig', [
            'customer_file' => $customerFile,
            'form' => $form->createView(),
            'providers' => $provider->findAll()
        ]);
    }


    /**
     * @Route("/{id}", name="customer_files_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Request $request, GlobalStatut $global,CustomerFiles $customerFile): Response
    {
        // if($customerFile->getGlobalStatut() === $global){
            if(!in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles()) || $customerFile->getInstaller() === $this->getUser()){
                return $this->render('customer_files/show.html.twig', [
                    'customer_file' => $customerFile,
                ]);
            }
            $this->addFlash('error', 'Vous n\'avez pas accès à cette fiche ');
            return $this->redirectToRoute('customer_files_index', [
                'global' => $this->session->get('global')
            ]);
        // }

        // $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        // return $this->redirectToRoute('customer_files_index', [
        //     'global' => $this->session->get('global')
        // ]);
    }

    public function getDocuments($docs){
        $documents = [];
        foreach($docs as $value){
            $documents[] = $value['name'];
        }
        return $documents;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/edit/global_πstatut", name="customer_files_edit_global_statut", methods={"GET","POST"})
     */
    public function editGlobalStatut(Request $request, GlobalStatut $global, CustomerFiles $customerFile)
    {
        // if($customerFile->getGlobalStatut() === $global)
        // {
            $form = $this->createForm(UpdateCustomerGlobalStatutType::class, $customerFile);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $customerFile->setCustomerStatut(null);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Le statut global de la fiche à bien été modifier !');
                return $this->redirectToRoute('customer_files_index', [
                    'global' => $this->session->get('global')
                ]);
            }

            return $this->render('customer_files/edit_global_statut.html.twig', [
                'customer_file' => $customerFile,
                'form' => $form->createView()
            ]);
        // }

        // $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        // return $this->redirectToRoute('customer_files_index', [
        //     'global' => $this->session->get('global')
        // ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/edit", name="customer_files_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GlobalStatut $global,CustomerFiles $customerFile, ProviderRepository $provider, CustomerFilesRepository $repository): Response
    {
        // if($customerFile->getGlobalStatut() === $global){
            $form = $this->createForm(UpdateCustomerFileType::class, $customerFile, array('global' => $global));
            $form->handleRequest($request);
    
            $password = $this->createForm(UpdateCustomerPasswordType::class, $customerFile);
            $password->handleRequest($request);
    
            $mail = $this->createForm(UpdateCustomerMailType::class, $customerFile);
            $mail->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                // $this->sendSmsToCustomerFile($customerFile, false);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'La fiche a bien été modifiée !');
                $this->smsAutoRepository->checkSms($customerFile);
                return $this->redirectToRoute('customer_files_show', [
                    'id' => $customerFile->getId(),
                    'global' => $this->session->get('global')
                ]);
    
            }else if($password->isSubmitted() && $password->isValid()){
                $this->getDoctrine()->getManager()->flush();
    
                $this->addFlash('success', 'Le mot de passe de la fiche a bien été modifié !');
                $this->smsAutoRepository->checkSms($customerFile);
                return $this->redirectToRoute('customer_files_show', [
                    'id' => $customerFile->getId(),
                    'global' => $this->session->get('global')
                ]);
    
            }else if($mail->isSubmitted() && $mail->isValid()){
                $this->getDoctrine()->getManager()->flush();
    
                $this->addFlash('success', 'L\'email de la fiche a bien été modifié !');
                $this->smsAutoRepository->checkSms($customerFile);
                return $this->redirectToRoute('customer_files_show', [
                    'id' => $customerFile->getId(),
                    'global' => $this->session->get('global')
                ]);
            }
            
            return $this->render('customer_files/edit.html.twig', [
                'customer_file' => $customerFile,
                'form' => $form->createView(),
                'mailForm' => $mail->createView(),
                'pwdForm' => $password->createView(),
                'providers' => $provider->findAll(),
                'params' => $repository->getProviderParams($customerFile->getProduct() ? $customerFile->getProduct()->getProvider() : null)
            ]);
        // }

        // $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        // return $this->redirectToRoute('customer_files_index', [
        //     'global' => $this->session->get('global')
        // ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}", name="customer_files_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GlobalStatut $global, CustomerFiles $customerFile): Response
    {
        // if($customerFile->getGlobalStatut() === $global){
            if ($this->isCsrfTokenValid('delete'.$customerFile->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($customerFile);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('customer_files_index', [
                'global' => $this->session->get('global')
            ]);
        // }
        // $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        // return $this->redirectToRoute('customer_files_index', [
        //     'global' => $this->session->get('global')
        // ]);
    }
}
