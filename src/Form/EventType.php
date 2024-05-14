<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('beginAt')
            ->add('endAt')
            ->add('hosts', EntityType::class, [
                'label' => 'Hosts',
                'translation_domain' => 'event',
                'autocomplete' => true,
                'class' => User::class,
                'data' => null,
            ])
            ->add('ticketCheckers', EntityType::class, [
                'label' => 'Ticket checkers',
                'translation_domain' => 'event',
                'autocomplete' => true,
                'class' => User::class,
                'data' => null,
            ])
            ->add('isPublic');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
