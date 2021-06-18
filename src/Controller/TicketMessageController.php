<?php

namespace App\Controller;

use App\Entity\GlobalStatut;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Form\TicketMessageType;
use App\Repository\TicketMessageRepository;
use App\Service\FindByRoles;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/customers/{global}/ticket/message", requirements={"global":"\d+"})
 */
class TicketMessageController extends AbstractController
{
    private $findByRoles;

    public function __construct(FindByRoles $findByRoles){
        $this->findByRoles = $findByRoles;
    }
    
    /**
     * @Route("/{id}", name="ticket_message_index", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function index(Request $request, GlobalStatut $global, Ticket $ticket, TicketMessageRepository $messages, NotificationService $notificationService): Response
    {
        if($ticket->getCustomerFile()->getGlobalStatut() === $global){
            if(in_array($this->getUser(), $ticket->getUsers()->toArray())|| $this->findByRoles->findByRole('ROLE_ADMIN', $this->getUser())){
                $message = new TicketMessage();
                $form = $this->createForm(TicketMessageType::class, $message);
                $form->handleRequest($request);
    
                // if ($form->isSubmitted() && $form->isValid()) {
                //     $message->setFromUser($this->getUser());
                //     $message->setTicket($ticket);
                //     $entityManager = $this->getDoctrine()->getManager();
                //     $entityManager->persist($message);
                //     $entityManager->flush();
                //     $ticket_id = $ticket->getId();
                //     // Send notification
                //     $notificationService->sendNotification($ticket->getUsers()->toArray(), "Nouveau message dans le ticket numero $ticket_id", "/ticket/message/$ticket_id", $message->getContent());
                //     return $this->redirectToRoute('ticket_message_index', [
                //         'id' => $ticket->getId()
                //     ]);
                // }
        
                return $this->render('ticket/messages/show.html.twig', [
                    'ticket' => $ticket,
                    'form' => $form->createView(),
                    'messages' => $messages->findById($ticket->getId())
                ]);
            }
    
            $this->addFlash('error', 'Vous n\'avez pas accès à ce ticket !');
            return $this->redirectToRoute('ticket_index', [
                'global' => $this->session->get('global')
            ]);
        }

        $this->addFlash('error', 'La fiche du ticket '.$ticket->getId().' n\'a pas pour statut global '.$global->getName());
        return $this->redirectToRoute('ticket_index', [
            'global' => $this->session->get('global')
        ]);
    }

    /**
     * @Route("/{id}", name="ticket_message_add", methods={"POST"}, requirements={"id":"\d+"})
    */
    public function addMessage(Request $request, GlobalStatut $global, Ticket $ticket, SerializerInterface $serializer){
        $body = json_decode($request->getContent());
 
        if($ticket->getCustomerFile()->getGlobalStatut() === $global){
            $message = new TicketMessage();
            $message->setFromUser($this->getUser());
            $message->setTicket($ticket);
            $message->setContent($body->message);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
            return new Response(json_encode([
                'status' => 200, 
                'data' => [
                    'from_user' => $message->getFromUser()->getUsername(), 'created_at' => $message->getCreatedAt()->format('d-m-Y H:m:i'), 
                    'message' => $message->getContent()
                    ]
                ]), Response::HTTP_OK, [
                'Content-Type' => 'application/json'
            ]);
        }

        return new Response(json_encode([
            'status' => 404, 
            'message' => 'La fiche du ticket '.$ticket->getId().' n\'a pas pour statut global '.$global->getName()
        ]), Response::HTTP_NOT_FOUND, [
            'Content-Type' => 'application/json'
        ]);
    }
}
