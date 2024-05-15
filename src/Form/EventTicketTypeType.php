<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\EventTicketType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventTicketTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'required' => true,
            ])
            ->add('requirements', CKEditorType::class, [
                'label' => 'Requirements',
                'required' => true,
            ])
            ->add('available', CheckboxType::class, [
                'label' => 'Available',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventTicketType::class,
        ]);
    }
}
