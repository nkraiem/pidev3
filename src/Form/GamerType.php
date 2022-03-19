<?php

namespace App\Form;

use App\Entity\Gamer;
use App\Form\HistoriqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
class GamerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')

            ->add('password')
            ->add('name')
            ->add('lastName')
            ->add('preference')
            ->add('expiriencePoint')
            ->add('status')

        ;
        $builder->add('Historique', HistoriqueType::class);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gamer::class,
        ]);
    }

}
