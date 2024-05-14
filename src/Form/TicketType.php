<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\EventTicketType;
use App\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use NumberFormatter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $event = (int) $options['event'];
        $formatter = new NumberFormatter('en-US', NumberFormatter::CURRENCY);
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone number',
                'translation_domain' => 'event',
                'required' => true,
            ])
            ->add('ticketType', EntityType::class, [
                'class' => EventTicketType::class,
                'label' => 'Ticket type',
                'translation_domain' => 'event',
                'query_builder' => function (EntityRepository $er) use ($event): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.event = :event')
                        ->andWhere('t.available = true')
                        ->setParameter(':event', $event);
                },
                'choice_label' => function (EventTicketType $eventTicketType) use ($formatter): string {
                    return $eventTicketType->getName().' ('.$formatter->formatCurrency($eventTicketType->getPrice(), 'EUR').')';
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'event' => null,
        ]);
    }
}
