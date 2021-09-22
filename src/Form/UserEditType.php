<?php

namespace App\Form;

use App\Entity\GlobalStatut;
use App\Entity\User;
use App\Service\FindByRoles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public $current_user;
    public $me;
    public $findByRoles;

    public function __construct(FindByRoles $findByRoles){
        $this->findByRoles = $findByRoles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->current_user = $options['data'];
        $this->me = $options['me'];
 
        $builder
        ->add('username', TextType::class, ['label' => 'Nom : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
        ->add('email', EmailType::class, ['label' => 'Email : <span class="text-danger">*</span>', 'required' => true, "label_html" => true]);
        if ($this->current_user !== $this->me) {
            if($this->findByRoles->findByRole('ROLE_ADMIN', $this->me)){
                $builder->add('roles', ChoiceType::class, ['choices' => ['Installateur' => 'ROLE_INSTALLATEUR', 'Ecrire des fiches' => 'ROLE_ALLOW_CREATE', 'Utilisateur' => 'ROLE_USER', 'Métreur' => 'ROLE_METREUR', 'Administrateur' => 'ROLE_ADMIN', 'Super Admin' => 'ROLE_SUPERADMIN', 'Developpeur' => 'ROLE_DEVELOPER'],
                    'expanded' => true,
                    'multiple' => true,
                    'required' => true,
                    'label' => 'Role(s) : <span class="text-danger">*</span>',
                    'label_html' => true,
                ]);
            }
        }else {
            $builder->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'mapped' => false,
                'required' => false,
                'first_options'  => array('label' => 'Modifier mon mot de passe'),
                'second_options' => array('label' => 'Répeter le mot de passe'),
            ));
        }
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'me' => null
        ]);
    }
}
