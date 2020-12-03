<?php

namespace App\Form;

use App\Entity\CustomerFilesStatut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerFilesStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom : <span class="text-danger">*</span>', 'required' => true, "label_html" => true])
            ->add('color', ColorType::class, [
                'label' => 'Choisir une couleur : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerFilesStatut::class,
        ]);
    }
}
