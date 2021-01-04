<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Ticket;
use App\Form\AddUserToTicketType;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TicketController extends AbstractController
{

    private $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy){
        $this->roleHierarchy = $roleHierarchy;
    }
    /**
     * @Route("/tickets", name="ticket_index", methods={"GET"})
     */
    public function index(TicketRepository $ticketRepository): Response
    {

        if(in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames($this->getUser()->getRoles()))){
            $tickets = $ticketRepository->findAll();
        }else{
            $tickets = $ticketRepository->findByUser($this->getUser());
        }
        
        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ticket/new", name="ticket_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $user): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($user->findAll() as $value) {
                $roles = $this->roleHierarchy->getReachableRoleNames($value->getRoles());
                if(in_array('ROLE_ADMIN', $roles)){
                    $ticket->addUser($value);
                }
            }
            $ticket->getCustomerFile() !== null && $ticket->getCustomerFile()->getInstaller() !== null && $ticket->addUser($ticket->getCustomerFile()->getInstaller());
            $ticket->setCreator($this->getUser());
            $ticket->addUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();
            $ticket_id = $ticket->getId();
            $notification = new Notification();
            $em = $this->getDoctrine()->getManager();
            $notification->setUrl("/ticket/$ticket_id");
            $notification->setTitle('Un nouveau ticket à été ouvert !');
            $em->persist($notification);
            foreach ($ticket->getUsers() as $value) if($value !== $this->getUser()) $value->addNotification($notification);
            $em->flush();
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
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/ticket/{id}/users", name="ticket_users", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function addUser(Request $request, Ticket $ticket, UserRepository $user): Response
    {
        $form = $this->createForm(AddUserToTicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($user->findAll() as $value) {
                $roles = $this->roleHierarchy->getReachableRoleNames($value->getRoles());
                if(in_array('ROLE_ADMIN', $roles)){
                    $ticket->addUser($value);
                }
            }
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
        if($this->getUser() === $ticket->getCreator() || in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames($this->getUser()->getRoles()))){
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
