<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventTicketType;
use App\Form\EventTicketTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventTicketTypeController extends AbstractController
{
    #[Route('/events/{id}/new-ticket-type', name: 'app_event_ticket_type_new')]
    public function new(Event $event, EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$event->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventTicketTypeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventTicketType = $form->getData();
            $eventTicketType->setEvent($event);
            $entityManager->persist($eventTicketType);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Successfully added new ticket type.'));

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        return $this->render('event_ticket_type/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event-ticket-type/{id}/edit', name: 'app_event_ticket_type_edit')]
    public function edit(EventTicketType $eventTicketType, EntityManagerInterface $entityManager, Request $request, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$eventTicketType->getEvent()->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventTicketTypeType::class, $eventTicketType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventTicketType = $form->getData();
            $entityManager->persist($eventTicketType);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Successfully edited ticket type.'));

            return $this->redirectToRoute('app_events_show', ['id' => $eventTicketType->getEvent()->getId()]);
        }

        return $this->render('event_ticket_type/edit.html.twig', [
            'event' => $eventTicketType->getEvent(),
            'type' => $eventTicketType,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/event-ticket-type/{id}/delete', name: 'app_event_ticket_type_delete', methods: ['POST'])]
    public function delete(EventTicketType $eventTicketType, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$eventTicketType->getEvent()->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $eventId = $eventTicketType->getEvent()->getId();
        $entityManager->remove($eventTicketType);
        $entityManager->flush();
        $this->addFlash('success', $translator->trans('An event ticket type deleted successfully.'));

        return $this->redirectToRoute('app_events_show', ['id' => $eventId]);
    }
}
