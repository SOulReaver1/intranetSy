<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class AddUserToTicketType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'label' => 'Utilisateur : <span class="text-danger">*</span>',
                'label_html' => true,
                'query_builder' => function(UserRepository $er){
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.roles NOT LIKE :admin')
                        ->andWhere('u.roles NOT LIKE :sadmin')
                        ->setParameter('sadmin', '%"'.'ROLE_SUPERADMIN'.'"%')
                        ->setParameter('admin', '%"'.'ROLE_ADMIN'.'"%');
                },
                'placeholder' => 'Aucun utilisateur',
                'required' => false,
                'attr' => ['class' => 'ui fluid dropdown'],
                'multiple' => true,
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
