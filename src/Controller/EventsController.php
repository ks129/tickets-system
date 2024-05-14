<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isGranted('ROLE_ADMIN')) {
            $queryBuilder = $eventRepository->findEventsForAdminQueryBuilder();
        } else {
            $queryBuilder = $eventRepository->findEventsForUserQueryBuilder($this->getUser());
        }

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            20
        );

        return $this->render('events/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/events/new', name: 'app_events_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(EventType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('New event successfully created.'));

            return $this->redirectToRoute('app_events');
        }

        return $this->render('events/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}/edit', name: 'app_events_edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$event->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Event successfully edited.'));

            return $this->redirectToRoute('app_events');
        }

        return $this->render('events/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}', name: 'app_events_show')]
    public function view(Event $event): Response
    {
        return $this->render('events/view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/delete', name: 'app_events_delete', methods: ['POST'])]
    public function delete(Event $event, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash('success', $translator->trans('An event deleted successfully.'));

        return $this->redirectToRoute('app_events');
    }
}
