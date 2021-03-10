<?php

namespace App\Controller;

use App\Entity\CustomerFiles;
use App\Entity\Ticket;
use App\Form\AddUserToTicketType;
use App\Form\TicketType;
use App\Repository\CustomerFilesRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use App\Service\FindByRoles;
use App\Service\Mailer;
use App\Service\NotificationService;
use Doctrine\ORM\QueryBuilder;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TicketController extends AbstractController
{

    private $findByRoles;

    public function __construct(FindByRoles $findByRoles){
        $this->findByRoles = $findByRoles;
    }
    
    /**
     * @Route("/tickets", name="ticket_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('customerFile', TextColumn::class, [
            'label' => 'Fiche client', 
            'field' => 'customerFile.name',
            'render' => function($data, $context){
                
                return $data ? sprintf("<a href='%s'>$data</a>", $this->generateUrl('customer_files_show', ['id' => $context->getCustomerFile()->getId()])) : 'Aucune fiche';
            }
        ])
        ->add('title', TextColumn::class, ['label' => 'Titre'])
        ->add('description', TextColumn::class, ['label' => 'Description'])
        ->add('created_at', DateTimeColumn::class, ['label' => 'Créer le', 'format' => 'd-m-Y H:i:s'])
        ->add('updated_at', DateTimeColumn::class, ['label' => 'Modifier le', 'format' => 'd-m-Y H:i:s'])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('ticket_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('ticket_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])
        ->addOrderBy('id', DataTable::SORT_ASCENDING)
        ->createAdapter(ORMAdapter::class, [
            'entity' => Ticket::class,
            'query' => function (QueryBuilder $builder) {
                if($this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
                    return $builder
                    ->select('t, customerFile')
                    ->from(Ticket::class, 't')
                    ->leftJoin('t.customer_file', 'customerFile');
                }else{
                    return $builder
                    ->select('t, customerFile')
                    ->from(Ticket::class, 't')
                    ->leftJoin('t.users', 'users')
                    ->leftJoin('t.customer_file', 'customerFile')
                    ->where('users = :val')
                    ->setParameter('val', $this->getUser());
                }
            }
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        
        return $this->render('ticket/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ticket/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request, Mailer $mailer, NotificationService $notificationService): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // Add all admin users
            if(!empty($this->findByRoles->findByRole('ROLE_ADMIN', null, false))){
                $ticket->addUser(...$this->findByRoles->findByRole('ROLE_ADMIN', null, false));
            }
            
            // Add the creator
            $ticket->addUser($this->getUser());
            // Add the installer
            $ticket->getCustomerFile() !== null && $ticket->getCustomerFile()->getInstaller() !== null && $ticket->addUser($ticket->getCustomerFile()->getInstaller());
            // Set creator
            $ticket->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();
            // Send notification
            $notificationService->sendNotification($ticket->getUsers()->toArray(), "Un nouveau ticket à été ouvert !", "/ticket/".$ticket->getId());
            // Send mail
            $mailer->sendMail($ticket->getUsers()->toArray(), 'Nouveau ticket Lergon\'Home', 'ticket/_email.html.twig', ['ticket' => $ticket]);
            $this->addFlash('success', 'Le ticket à bien été créer !');  
            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ticket/{id}", name="ticket_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Ticket $ticket): Response
    {
        if(in_array($this->getUser(), $ticket->getUsers()->toArray()) || $this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
            ]);
        }
        
        $this->addFlash('error', 'Vous n\'avez pas accès à cette fiche !');
        return $this->redirectToRoute('ticket_index');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/ticket/{id}/users", name="ticket_users", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function addUser(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(AddUserToTicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Add all admin users to update changes
            $ticket->addUser(...$this->findByRoles->findByRole('ROLE_ADMIN'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_show', [
                'id' => $ticket->getId()
            ]);
        }

        return $this->render('ticket/adduser.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ticket/{id}/edit", name="ticket_edit", methods={"GET","POST"})
    */
    public function edit(Request $request, Ticket $ticket): Response
    {
        // If I am the creator or an administrator, i can edit the ticket
        if($this->getUser() === $ticket->getCreator() || $this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('ticket_index');
            }
    
            return $this->render('ticket/edit.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('ticket_show', ['id' => $ticket->getId()]);
        
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/admin/ticket/{id}", name="ticket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }
}
