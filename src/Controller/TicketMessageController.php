<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Form\TicketMessageType;
use App\Repository\TicketMessageRepository;
use App\Service\FindByRoles;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket/message")
 */
class TicketMessageController extends AbstractController
{
    private $findByRoles;

    public function __construct(FindByRoles $findByRoles){
        $this->findByRoles = $findByRoles;
    }
    
    /**
     * @Route("/{id}", name="ticket_message_index", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function index(Request $request, Ticket $ticket, TicketMessageRepository $messages, NotificationService $notificationService, MessageBusInterface $bus): Response
    {
        
        if(in_array($this->getUser(), $ticket->getUsers()->toArray())|| $this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
            $message = new TicketMessage();
            $form = $this->createForm(TicketMessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $message->setFromUser($this->getUser());
                $message->setTicket($ticket);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($message);
                $entityManager->flush();
                $ticket_id = $ticket->getId();
                // Send notification
                $notificationService->sendNotification($ticket->getUsers()->toArray(), "Nouveau message dans le ticket numero $ticket_id", "/ticket/message/$ticket_id", $message->getContent());
                $update = new Update(
                    $this->generateUrl('ticket_message_index', 
                    ['id' => $ticket->getId()]),
                    json_encode(['createdAt' => $message->getCreatedAt()->format('d-m-Y H:i:s'),'username' => $this->getUser()->getUsername(), 'message' => $message->getContent()])
                );
                $bus->dispatch($update);
                return $this->redirectToRoute('ticket_message_index', [
                    'id' => $ticket->getId()
                ]);
            }
    
            return $this->render('ticket/messages/show.html.twig', [
                'ticket' => $ticket,
                'form' => $form->createView(),
                'messages' => $messages->findById($ticket->getId())
            ]);
        }

        $this->addFlash('error', 'Vous n\'avez pas accès à ce ticket !');
        return $this->redirectToRoute('ticket_index');
        
    }
}
