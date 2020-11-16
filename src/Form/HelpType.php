<?php

namespace App\Form;

use App\Entity\Help;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statut', null, ['required' => true, 'label' => 'Statut du ticket : <span class="text-danger">*</span>', 'label_html' => true])
            ->add('title', TextType::class, ['required' => true, 'label' => 'Titre : <span class="text-danger">*</span>', 'label_html' => true])
            ->add('description', TextareaType::class, ['required' => true, 'label' => 'Description : <span class="text-danger">*</span>', 'label_html' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Help::class,
        ]);
    }
}
