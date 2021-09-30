<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use App\Entity\Ticket;
use App\Entity\TicketStatut;
use App\Entity\User;
use App\Repository\CustomerFilesRepository;
use App\Repository\TicketStatutRepository;
use App\Service\FindByRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketType extends AbstractType
{
    private $findByRoles;
    private $session;

    public function __construct(FindByRoles $findByRoles, SessionInterface $session){
        $this->findByRoles = $findByRoles;
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statut', EntityType::class, [
                'class' => TicketStatut::class,
                'label' => 'Choisir un statut : <span class="text-danger">*</span>',
                'label_html' => true,
                'query_builder' => function(TicketStatutRepository $ticketStatutRepository) {
                    return $ticketStatutRepository->createQueryBuilder('c')
                    ->where('c.global_statut = :g')
                    ->setParameter('g', $this->session->get('global'));
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le statut du ticket ne peut pas être vide !',
                    ]),
                ],
            ])
            ->add('customer_file', EntityType::class, [
                'class' => CustomerFiles::class,
                'label' => 'Choisir une fiche client :',
                'required' => false,
                'query_builder' => function(CustomerFilesRepository $customerFilesRepository){
                    return $customerFilesRepository->createQueryBuilder('c')
                    ->where('c.global_statut = :g')
                    ->setParameter('g', $this->session->get('global'));
                },
                'attr' => ['class' => 'ui fluid search dropdown'],
                'placeholder' => 'Aucune fiche client'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'required' => true,
                'attr' => ['class' => 'ui fluid search dropdown'],
                "data" => [],
                'label' => 'Utilisateurs : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
