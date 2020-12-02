<?php

namespace App\Controller;

use App\Entity\Help;
use App\Form\HelpType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
    /**
     * @Route("/dev/help/index", name="help_index", methods={"GET"})
     */
    public function index(HelpRepository $helpRepository): Response
    {
        return $this->render('help/index.html.twig', [
            'helps' => $helpRepository->findAll(),
        ]);
    }

    /**
     * @Route("/help/new", name="help_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $user, MailerInterface $mailer): Response
    {
        $help = new Help();
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $help->setUserId($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($help);
            $entityManager->flush();
            $emails = ['pro.ilanjourno@gmail.com', 'ilan.journo555@gmail.com'];
            $email = (new TemplatedEmail())
            ->from(new Address('contact@lergonhome.fr', 'Intranet Lergon\'Home'))
            ->to(...$emails)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Nouvelle demande d\'aide Intranet Lergon\'Home')
            ->context(['help' => $help])
            ->htmlTemplate('help/_email.html.twig');
            $mailer->send($email);
            $this->addFlash('success', 'Merci ! Votre bug à bien été signalé !');  
            return $this->redirectToRoute('default');
        }

        return $this->render('help/new.html.twig', [
            'help' => $help,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dev/help/{id}", name="help_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Help $help): Response
    {
        if(!$help->getReadAt()){
            $help->setReadAt(new DateTime('now'));
        }
        return $this->render('help/show.html.twig', [
            'help' => $help,
        ]);
    }

    /**
     * @Route("/dev/help/{id}/edit", name="help_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Help $help): Response
    {
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('help_index');
        }

        return $this->render('help/edit.html.twig', [
            'help' => $help,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dev/help/{id}", name="help_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Help $help): Response
    {
        if ($this->isCsrfTokenValid('delete'.$help->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($help);
            $entityManager->flush();
        }

        return $this->redirectToRoute('help_index');
    }
}
