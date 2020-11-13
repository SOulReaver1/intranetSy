<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('Sexe', ChoiceType::class, 
            [
                'choices' => 
                [   'Monsieur' => 'Monsieur', 
                    'Madame' => 'Madame'
                ], 
                'row_attr' => ['class' => 'col-md-6'],
                'expanded' => true, 
                'label' => 'Sexe : <span class="text-danger">*</span>', 
                'label_html' => true,
                'required' => true
            ])
            ->add('Name', TextType::class, 
            [
                'label' => 'Nom complet : <span class="text-danger">*</span>', 'label_html' => true, 
                'required' => true,
                'row_attr' => ['class' => 'col-md-6'],

            ])
            ->add('Adresse', TextType::class, 
            [
                'label' => 'Adresse :',
                'row_attr' => ['class' => 'col-md-6'],

            ])
            ->add('city', TextType::class, [
                'label' => 'Ville :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('zip_code', IntegerType::class, [
                'label' => 'Code postal :',
                'row_attr' => ['class' => 'col-md-6'],
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
            ->add('annex_quote_description', TextareaType::class, [
                'label' => 'Description :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('annex_quote_commentary', TextareaType::class, [
                'label' => 'Commentaire :',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('client_statut_id', null, [
                'label' => 'Statut du client : ',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('customer_statut', null, [
                'label' => 'Statut du dossier : ',
                'row_attr' => ['class' => 'col-md-6'],
            ])
            ->add('users_id', null, [
                'label' => 'Installateurs et assistantes sociales : ',
                'row_attr' => ['class' => 'col'],
                'attr' => ['class' => 'ui fluid dropdown']
            ])
            ->add('customer_source', null, [
                'label' => 'La source : ',
                'row_attr' => ['class' => 'col'],
                'attr' => ['class' => 'ui fluid dropdown']
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
