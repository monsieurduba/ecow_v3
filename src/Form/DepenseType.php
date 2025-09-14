<?php

namespace App\Form;

use App\Entity\Depense;
use App\Entity\TypeDepense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('utilisateur')
            ->add('montant')
            ->add('detail')
            ->add('Date', null, [
                'widget' => 'single_text',
            ])
            ->add('type', EntityType::class, [
                'class' => TypeDepense::class,
                'choice_label' => 'id',
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
