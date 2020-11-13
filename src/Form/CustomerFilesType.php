<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
                'expanded' => true, 
                'label' => 'Sexe : <span class="text-danger">*</span>', 
                'label_html' => true,
                'required' => true
            ])
            ->add('Name', TextType::class, 
            [
                'label' => 'Nom : <span class="text-danger">*</span>', 'label_html' => true, 
                'required' => true
            ])
            ->add('Adresse', TextType::class, 
            [
                'label' => 'Adresse :',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville :',
            ])
            ->add('zip_code', IntegerType::class, [
                'label' => 'Code postal :',
            ])
            ->add('home_phone', TelType::class, [
                'label' => 'Téléphone fixe :',
            ])
            ->add('cellphone', TelType::class, [
                'label' => 'Téléphone portable :',
            ])
            ->add('referent_name', TextType::class, 
            [
                'label' => 'Nom :', 
            ])
            ->add('referent_phone', TelType::class, [
                'label' => 'Téléphone :',
            ])
            ->add('referent_statut', TextType::class, [
                'label' => 'Etat matrimonial :',
            ])
            ->add('stairs', CheckboxType::class, [
                'label' => 'Escalier'
            ])
            ->add('mail_al', EmailType::class, [
                'label' => 'Mail AL'
            ])
            ->add('password_al', PasswordType::class, [
                'label' => 'Mot de passe AL'
            ])
            ->add('annex_quote', CheckboxType::class, [
                'label' => 'Devis annexe'
            ])
            ->add('annex_quote_description', TextareaType::class, [
                'label' => 'Description :'
            ])
            ->add('annex_quote_commentary', TextareaType::class, [
                'label' => 'Description :'
            ])
            ->add('client_statut_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerFiles::class,
        ]);
    }
}
