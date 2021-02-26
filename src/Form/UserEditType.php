<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('username', TextType::class, ['label' => 'Nom : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
        ->add('email', EmailType::class, ['label' => 'Email : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
        ->add('roles', ChoiceType::class, ['choices' => ['Installateur' => 'ROLE_INSTALLATEUR', 'Ecrire des fiches' => 'ROLE_ALLOW_CREATE', 'Utilisateur' => 'ROLE_USER', 'Métreur' => 'ROLE_METREUR', 'Administrateur' => 'ROLE_ADMIN', 'Super Admin' => 'ROLE_SUPERADMIN', 'Developpeur' => 'ROLE_DEVELOPER'],
            'expanded' => true,
            'multiple' => true,
            'required' => true,
            'label' => 'Role(s) : <span class="text-danger">*</span>',
            'label_html' => true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
