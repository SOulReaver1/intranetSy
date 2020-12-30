<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Form\TicketMessageType;
use App\Repository\TicketMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ticket/message")
 */
class TicketMessageController extends AbstractController
{
    /**
     * @Route("/{id}", name="ticket_message_index", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function index(Request $request, Ticket $ticket, TicketMessageRepository $messages): Response
    {
        $message = new TicketMessage();
        $form = $this->createForm(TicketMessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setFromUser($this->getUser());
            $message->setTicket($ticket);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            $notification = new Notification();
            $ticket_id = $ticket->getId();
            $em = $this->getDoctrine()->getManager();
            $notification->setUrl("/ticket/message/$ticket_id");
            $notification->setTitle("Nouveau message dans le ticket numero $ticket_id");
            $notification->setDescription($message->getContent());
            $em->persist($notification);
            foreach ($ticket->getUsers() as $value) if($value !== $this->getUser()) $value->addNotification($notification);
            $em->flush();
           
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
}
