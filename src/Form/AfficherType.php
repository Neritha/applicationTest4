<?php

namespace App\Form;

use App\Entity\Fichier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AfficherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('field_name')
            ->add('fichier', EntityType::class,[
                'class'=>Fichier::class,
                'choice_label'=>'nomOriginal',
                'label' => false,
                'attr'=>[
                    'class'=>"selectStyles m-2",]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class'=> 'btn bg-primary text-white m-2' ], 
                'row_attr' => ['class' => 'text-center'],
                'label'=> 'OK'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
