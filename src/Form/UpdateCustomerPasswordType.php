<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateCustomerPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('password_al', TextType::class, [
            'required' => false,
            'label' => 'Mot de passe AL',
            'attr' => ['autocomplete' => 'off'],
            'row_attr' => ['class' => 'col-md-6'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerFiles::class,
        ]);
    }
}
