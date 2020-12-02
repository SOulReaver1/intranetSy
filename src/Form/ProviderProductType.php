<?php

namespace App\Form;

use App\Entity\Provider;
use App\Entity\ProviderParam;
use App\Entity\ProviderProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('provider', EntityType::class, [
                'label' => 'Le fournisseur : <span class="text-danger">*</span>',
                'label_html' => true,
                'class' => Provider::class
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom : <span class="text-danger">*</span>',
                'label_html' => true
            ])
            ->add('params', EntityType::class, [
                'label' => 'Le(s) paramètre(s) :',
                'class' => ProviderParam::class,
                'required' => true,
                'attr' => ['class' => 'ui fluid dropdown'],
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProviderProduct::class,
        ]);
    }
}
