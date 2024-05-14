<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventNews;
use App\Form\EventNewsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventNewsController extends AbstractController
{
    #[Route('/event-news/{id}', name: 'app_event_news_view')]
    public function view(EventNews $eventNews): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$eventNews->getEvent()->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('event_news/view.html.twig', [
            'news' => $eventNews,
        ]);
    }

    #[Route('/events/{id}/create-news', name: 'app_event_create_news')]
    public function create(Event $event, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$event->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventNewsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EventNews $news */
            $news = $form->getData();
            $news->setEvent($event);
            $news->setCreatedAt(new \DateTimeImmutable());
            $news->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('A news article added successfully.'));

            return $this->redirectToRoute('app_events_show', ['id' => $event->getId()]);
        }

        return $this->render('event_news/new.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/event-news/{id}/edit', name: 'app_event_edit_news')]
    public function edit(EventNews $eventNews, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$eventNews->getEvent()->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EventNewsType::class, $eventNews);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news = $form->getData();
            $news->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($news);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('A news article updated successfully.'));

            return $this->redirectToRoute('app_events_show', ['id' => $eventNews->getEvent()->getId()]);
        }

        return $this->render('event_news/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $eventNews->getEvent(),
        ]);
    }

    #[Route('/event-news/{id}/delete', name: 'app_event_delete_news')]
    public function delete(EventNews $eventNews, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$eventNews->getEvent()->getHosts()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $eventId = $eventNews->getEvent()->getId();
        $entityManager->remove($eventNews);
        $entityManager->flush();
        $this->addFlash('success', $translator->trans('A news article deleted successfully.'));

        return $this->redirectToRoute('app_events_show', ['id' => $eventId]);
    }
}
