<?php

namespace App\Form;

use App\Entity\CustomerFiles;
use App\Entity\GlobalStatut;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateCustomerGlobalStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('global_statut', EntityType::class, [
                'label' => 'Statut global : <span class="text-danger">*</span>',
                'label_html' => true,
                'class' => GlobalStatut::class,
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
