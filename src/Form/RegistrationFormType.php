<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('roles', ChoiceType::class, ['choices' => ['Installateur' => 'ROLE_INSTALLATEUR', 'Assistante sociale' => 'ROLE_ASSIST', 'Ecrire des fiches' => 'ROLE_ALLOW_CREATE', 'Utilisateur' => 'ROLE_USER', 'Administrateur' => 'ROLE_ADMIN'],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Role(s) : <span class="text-danger">*</span>',
                'label_html' => true,
            ])
            ->add('username', TextType::class, ['label' => 'Nom : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
            ->add('email', EmailType::class, ['label' => 'Email : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le mot de passe ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Mot de passe : <span class="text-danger">*</span>', 
                'required' => true, 
                "label_html" => true
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions d\'utilisation',
                    ]),
                ],
                'label' => 'Condition d\'utilisation',
                'label_html' => true
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
