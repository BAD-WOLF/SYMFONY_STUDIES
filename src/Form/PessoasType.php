<?php

namespace App\Form;

use App\Entity\Pessoas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PessoasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cpf', options: [
                'attr' => [
                    'class' => "content"
                ]
            ])
            ->add('nome', options: [
                'attr' => [
                    'class' => "content"
                ]
            ])
            ->add('nascimento', options: [
                'attr' => [
                    'class' => "content"
                ]
            ])
        ;
        $builder->setAttributes(['attr' => ['class' => 'content']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pessoas::class,
        ]);
    }
}
