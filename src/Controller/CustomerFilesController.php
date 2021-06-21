<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\GlobalStatut;
use App\Entity\Provider;
use App\Form\CustomerFilesType;
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

    public function __construct(SessionInterface $sessionInterface, SmsAutoRepository $smsAutoRepository, SendSms $sendSms, ClientStatutDocumentRepository $clientStatutDocumentRepository)
    {
        $this->session = $sessionInterface;
        $this->smsAutoRepository = $smsAutoRepository;
        $this->sendSms = $sendSms;
        $this->clientStatutDocumentRepository = $clientStatutDocumentRepository;
    }

    /**
     * @Route("/", name="customer_files_index", methods={"GET", "POST"})
     */
    public function index(Request $request, GlobalStatut $global, CustomerFilesStatutRepository $customerFilesStatutRepository, DataTableFactory $dataTableFactory)
    {        
        $this->globalStatut = $global;

        if($request->isMethod('get')){
            $this->session->remove('statut');
            if($request->query->get('statut')){
                $this->session->set('statut', $request->query->get('statut'));
            }
            $this->session->set('global', $this->globalStatut->getId());
        }

        $table = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => '#'])
            ->add('global_statut', TextColumn::class, [
                'field' => 'c.global_statut.name',
                'label' => 'Statut global'
            ])
            ->add('customer_statut', TextColumn::class, [
                'field' => 'customer_statut.name', 
                'label' => 'Statut dossier'
            ])
            ->add('name', TextColumn::class, ['label' => 'Nom complet'])
            ->add('date_expertise', DateTimeColumn::class, ['label' => 'Date d\'expertise'])
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
                    if($this->session->get('statut')){
                        return $builder
                        ->select('e, customer_statut')
                        ->andWhere('e.global_statut = :g')
                        ->andWhere('customer_statut.id = :statut')
                        ->setParameter("g", $this->globalStatut)
                        ->setParameter("statut", $this->session->get('statut'))
                        ->from(CustomerFiles::class, 'e')
                        ->leftJoin('e.customer_statut', 'customer_statut');
                    }
                    return $builder
                        ->select('e, customer_statut')
                        ->where('e.global_statut = :g')
                        ->setParameter("g", $this->globalStatut)
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
            'statuts' => $customerFilesStatutRepository->findAllByOrder($global),
            'datatable' => $table
        ]);
    }

    public function sendSmsToCustomerFile(CustomerFiles $customerFile, bool $new){
        if($customerFile->getCellphone() && $customerFile->getMessage()){
            if($new) $this->sendStep1($customerFile);
            if($customerFile->getDateFootage() && new DateTime('now') < $customerFile->getDateFootage() && $customerFile->getMessageSend() === false){
                $this->sendLastSteps($customerFile);
            }
        }
    }

    private function sendStep1(CustomerFiles $customerFile){
        $step1 = $this->smsAutoRepository->findOneBy(['step' => 1]);
        $pattern = ['/{documents}/', '/{name}/'];
        $documents = [];
        foreach($this->clientStatutDocumentRepository->findDocumentsByRequired($customerFile->getClientStatutId(), true) as $value){
            $documents[] = $value['name'];
        }
        $remplacement = [implode(', ', $documents), $customerFile->getName()];
        $step1Content = preg_replace($pattern, $remplacement, $step1->getContent());
        $this->sendSms->send($step1Content, [$customerFile->getCellphone()], $step1);
    }

    private function sendLastSteps(CustomerFiles $customerFile){
        // Get informations
        $pattern = ['/{documents}/', '/{name}/', '/{dateMetrage}/'];
        $documents = [];
        foreach($this->clientStatutDocumentRepository->findDocumentsByRequired($customerFile->getClientStatutId(), true) as $value){
            $documents[] = $value['name'];
        }
        $dateToString = $customerFile->getDateFootage()->format('d/m/Y H:i:s');
        $remplacement = [implode(', ', $documents), $customerFile->getName(), $dateToString];
        // -------------------------------------------
        // Send step 2
        $step2 = $this->smsAutoRepository->findOneBy(['step' => 2]);
        $step2Content = preg_replace($pattern, $remplacement, $step2->getContent());
        $this->sendSms->send($step2Content, [$customerFile->getCellphone()], $step2);
        // ------------------------
        // Program step 3, one day before footage
        $step3 = $this->smsAutoRepository->findOneBy(['step' => 3]);
        $step3Content = preg_replace($pattern, $remplacement, $step3->getContent());
        $this->sendSms->send($step3Content, [$customerFile->getCellphone()], $step3, $customerFile->getDateFootage(), 1440);
        // -------------------
        // Program step 4, one hour before footage
        $step4 = $this->smsAutoRepository->findOneBy(['step' => 4]);
        $step4Content = preg_replace($pattern, $remplacement, $step4->getContent());
        $this->sendSms->send($step4Content, [$customerFile->getCellphone()], $step4,  $customerFile->getDateFootage(), 60);
        $customerFile->setMessageSend(true);
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
            $this->sendSmsToCustomerFile($customerFile, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customerFile);
            $entityManager->flush();
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
        
        return new JsonResponse($repository->getPhones());
    }


    /**
     * @Route("/{id}", name="customer_files_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Request $request, GlobalStatut $global,CustomerFiles $customerFile): Response
    {
        if($customerFile->getGlobalStatut() === $global){
            if(!in_array('ROLE_INSTALLATEUR', $this->getUser()->getRoles()) || $customerFile->getInstaller() === $this->getUser()){
                return $this->render('customer_files/show.html.twig', [
                    'customer_file' => $customerFile,
                ]);
            }
            $this->addFlash('error', 'Vous n\'avez pas accès à cette fiche ');
            return $this->redirectToRoute('customer_files_index', [
                'global' => $this->session->get('global')
            ]);
        }

        $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        return $this->redirectToRoute('customer_files_index', [
            'global' => $this->session->get('global')
        ]);
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
        if($customerFile->getGlobalStatut() === $global)
        {
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
        }

        $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        return $this->redirectToRoute('customer_files_index', [
            'global' => $this->session->get('global')
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}/edit", name="customer_files_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GlobalStatut $global,CustomerFiles $customerFile, ProviderRepository $provider, CustomerFilesRepository $repository): Response
    {
        if($customerFile->getGlobalStatut() === $global){
            $form = $this->createForm(UpdateCustomerFileType::class, $customerFile, array('global' => $global));
            $form->handleRequest($request);
    
            $password = $this->createForm(UpdateCustomerPasswordType::class, $customerFile);
            $password->handleRequest($request);
    
            $mail = $this->createForm(UpdateCustomerMailType::class, $customerFile);
            $mail->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->sendSmsToCustomerFile($customerFile, false);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'La fiche a bien été modifiée !');
                return $this->redirectToRoute('customer_files_show', [
                    'id' => $customerFile->getId(),
                    'global' => $this->session->get('global')
                ]);
    
            }else if($password->isSubmitted() && $password->isValid()){
                $this->getDoctrine()->getManager()->flush();
    
                $this->addFlash('success', 'Le mot de passe de la fiche a bien été modifié !');
                return $this->redirectToRoute('customer_files_show', [
                    'id' => $customerFile->getId(),
                    'global' => $this->session->get('global')
                ]);
    
            }else if($mail->isSubmitted() && $mail->isValid()){
                $this->getDoctrine()->getManager()->flush();
    
                $this->addFlash('success', 'L\'email de la fiche a bien été modifié !');
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
        }

        $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        return $this->redirectToRoute('customer_files_index', [
            'global' => $this->session->get('global')
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{id}", name="customer_files_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GlobalStatut $global, CustomerFiles $customerFile): Response
    {
        if($customerFile->getGlobalStatut() === $global){
            if ($this->isCsrfTokenValid('delete'.$customerFile->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($customerFile);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('customer_files_index', [
                'global' => $this->session->get('global')
            ]);
        }
        $this->addFlash('error', "La fiche ".$customerFile->getId()." n'a pas le statut global : ".$global->getName());
        return $this->redirectToRoute('customer_files_index', [
            'global' => $this->session->get('global')
        ]);
    }
}
