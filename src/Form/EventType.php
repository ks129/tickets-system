<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

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
            ->add('beginAt', DateTimeType::class, [
                'label' => 'Begins at',
                'translation_domain' => 'event',
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'Ends at',
                'translation_domain' => 'event',
            ])
            ->add('hosts', EntityType::class, [
                'label' => 'Hosts',
                'translation_domain' => 'event',
                'autocomplete' => true,
                'class' => User::class,
                'multiple' => true,
            ])
            ->add('ticketCheckers', EntityType::class, [
                'label' => 'Ticket checkers',
                'translation_domain' => 'event',
                'autocomplete' => true,
                'class' => User::class,
                'multiple' => true,
            ])
            ->add('isPublic')
            ->add('ticketsAvailable');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $event->getForm()->remove('hosts');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
