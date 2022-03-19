<?php

namespace App\Form;

use App\Entity\Equipes;
use App\Entity\Matchs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateMatch')
            ->add('equipe1', EntityType::class,[
                'class'=>Equipes::class,
                'choice_label' => 'nom'

            ]
            )
            ->add('equipe2', EntityType::class,[
                'class'=>Equipes::class,
                'choice_label' => 'nom'

            ]
            )
            ->add('refMatch')
            ->add('scoreA')
            ->add('scoreB')
            ->add('tournoi')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}
