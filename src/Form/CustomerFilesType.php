<?php

namespace App\Form;

use App\Entity\ClientStatut;
use App\Entity\CustomerFiles;
use App\Entity\CustomerFilesStatut;
use App\Entity\CustomerSource;
use App\Entity\ProviderProduct;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sexe', ChoiceType::class, 
            [
                'choices' => 
                [   'Monsieur' => 'Monsieur', 
                    'Madame' => 'Madame'
                ], 
                'row_attr' => ['class' => 'col'],
                'required' => true,
                'label' => 'Sexe : <span class="text-danger">*</span>',
                'label_html' => true
            ])
            ->add('name', TextType::class, 
            [
                'label' => 'Nom complet : <span class="text-danger">*</span>', 'label_html' => true, 
                'required' => true,
                'row_attr' => ['class' => 'col'],

            ])
            ->add('address', TextType::class, 
            [
                'attr' => [
                    'onFocus' => 'geolocate()',
                ],
                'label' => 'Adresse :',
                'row_attr' => ['class' => 'col'],
                'attr' => [
                    'disabled' => true
                ]

            ])
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            ->add('address_complement', TextType::class, 
            [
                'label' => 'Complement d\'adresse :',
                'row_attr' => ['class' => 'col-md-6'],

            ])
            ->add('route_number', IntegerType::class, 
            [
                'label' => 'N° :',
                'row_attr' => ['class' => 'col-md-2'],
                'attr' => [
                    'disabled' => true
                ]

            ])
            ->add('state', TextType::class, 
            [
                'label' => 'Etat :',
                'row_attr' => ['class' => 'col-md-6'],
                'attr' => [
                    'disabled' => true
                ]

            ])
            ->add('country', TextType::class, 
            [
                'label' => 'Pays :',
                'row_attr' => ['class' => 'col-md-6'],
                'attr' => [
                    'disabled' => true
                ]

            ])
            ->add('city', TextType::class, [
                'label' => 'Ville :',
                'row_attr' => ['class' => 'col-md-6'],
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('zip_code', IntegerType::class, [
                'label' => 'Code postal :',
                'row_attr' => ['class' => 'col-md-6'],
                'attr' => [
                    'disabled' => true
                ]
            ])
            ->add('home_phone', TelType::class, [
                'label' => 'Téléphone fixe :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('cellphone', TelType::class, [
                'label' => 'Téléphone portable :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('referent_name', TextType::class, 
            [
                'label' => 'Nom complet :', 
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('referent_phone', TelType::class, [
                'label' => 'Téléphone :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('referent_statut', TextType::class, [
                'label' => 'Etat matrimonial :',
                'row_attr' => ['class' => 'col'],
            ])
            ->add('stairs', CheckboxType::class, [
                'label' => 'Escalier',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('mail_al', EmailType::class, [
                'label' => 'Mail AL',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('password_al', PasswordType::class, [
                'label' => 'Mot de passe AL',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('annex_quote', CheckboxType::class, [
                'label' => 'Devis annexe',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('commentary', TextareaType::class, [
                'label' => 'Commentaire :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('client_statut_id', EntityType::class, [
                'class' => ClientStatut::class,
                'label' => 'Statut du client : ',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('customer_statut', EntityType::class, [
                'class' => CustomerFilesStatut::class,
                'label' => 'Statut du dossier : ',
                'row_attr' => ['class' => 'col'],
            ])
            ->add('customer_source', EntityType::class, [
                'class' => CustomerSource::class,
                'label' => 'La source : ',
                'row_attr' => ['class' => 'col'],
                'attr' => ['class' => 'ui fluid dropdown']
            ])
            ->add('installer', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Aucun installateur',
                'query_builder' => function (UserRepository $user) {
                    return $user->createQueryBuilder('u')
                    ->orderBy('u.roles', 'ASC')
                    ->where('u.roles LIKE :roles')
                    ->setParameter('roles', '%"ROLE_INSTALLATEUR"%');
                },
                'label' => 'Installateur :',
                'row_attr' => ['class' => 'col-md-6']
            ])
            ->add('product', EntityType::class, [
                'label' => 'Le produit :',
                'class' => ProviderProduct::class,
                'row_attr' => ['id' => 'providerProductsParent', 'class' => 'col-md-6', 'style' => 'display: none']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerFiles::class,
        ]);
    }
}
