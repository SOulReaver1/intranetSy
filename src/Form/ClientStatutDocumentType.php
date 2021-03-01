<?php

namespace App\Form;

use App\Entity\ClientStatutDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientStatutDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required', ChoiceType::class, [
                'label' => 'A demander au client : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom : <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true
            ])
            ->add('client_statut', null, [
                'label' => 'Statut(s) <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => true,
                'attr' => ['class' => 'ui fluid dropdown']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClientStatutDocument::class,
        ]);
    }
}
