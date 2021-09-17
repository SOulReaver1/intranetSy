<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="user_index", methods={"GET", "POST"})
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {

        $table = $dataTableFactory->create()
        ->add('id', NumberColumn::class, ['label' => '#'])
        ->add('username', TextColumn::class, ['label' => 'Nom complet'])
        ->add('email', TextColumn::class, ['label' => 'Email'])
        ->add('roles', TextColumn::class, [
            "data" => function($data, $v){
                return implode(', ', $v);
            },
            'label' => 'Roles'
        ])
        ->add('actions', TextColumn::class, [
            'data' => function($context) {
                return $context->getId();
            }, 
            'render' => function($value, $context){
                $show = sprintf('<a href="%s" class="btn btn-primary">Regarder</a>', $this->generateUrl('user_show', ['id' => $value]));
                $edit = sprintf('<a href="%s" class="btn btn-primary">Modifier</a>', $this->generateUrl('user_edit', ['id' => $value]));
                return $show.$edit;
            }, 
            'label' => 'Actions'
        ])
        ->createAdapter(ORMAdapter::class, [
            'entity' => User::class,
        ])->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('user/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/admin/user{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/me", name="my_profile", methods={"GET", "POST"})
    */
    public function me(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $me = $this->getUser();
        $form = $this->createForm(UserEditType::class, $me, ['me' => $me]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            if($form->get('plainPassword')->getData()) {
                $me->setPassword(
                    $passwordEncoder->encodePassword(
                        $me,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre profile à bien été modifié !');
            return $this->redirectToRoute('my_profile');
        }

        return $this->render('user/me.html.twig', [
            'me' => $me,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $me = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user, ['me' => $me]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre utilisateur à bien été modifier !');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Votre utilisateur à bien été supprimer !');
        return $this->redirectToRoute('user_index');
    }
}
