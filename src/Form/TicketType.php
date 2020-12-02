<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use App\Entity\Ticket;
use App\Entity\TicketStatut;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statut_id', EntityType::class, [
                'class' => TicketStatut::class,
                'label' => 'Choisir un statut : <span class="text-danger">*</span>',
                'label_html' => true,
            ])
            ->add('customer_file', EntityType::class, [
                'class' => CustomerFiles::class,
                'label' => 'Choisir une fiche : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre : <span class="text-danger">*</span>',
                'label_html' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description : <span class="text-danger">*</span>',
                'label_html' => true,
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
