<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckingController extends AbstractController
{
    #[Route('/events/{id}/checking', name: 'app_event_checking')]
    public function index(Event $event): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$event->getHosts()->contains($this->getUser()) && !$event->getTicketCheckers()->contains($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('checking.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/check', name: 'app_check', methods: ['POST'])]
    public function check(Request $request, TicketRepository $ticketRepository, EventRepository $eventRepository, EntityManagerInterface $entityManager): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $eventId = (int) $parameters['eventId'];
        $ticketNumber = $parameters['ticketNumber'];

        $event = $eventRepository->find($eventId);
        if (!$event) {
            throw $this->createNotFoundException();
        }

        $ticket = $ticketRepository->findByEventAndTicketNumber($event, $ticketNumber);
        if (!$ticket) {
            return new JsonResponse([
                'html' => $this->renderView('check_response.html.twig', ['success' => false, 'warning' => false, 'ticket' => null]),
            ]);
        }

        if (0 !== $ticket->getTimesUsed() || !$ticket->isPaid()) {
            $html = $this->renderView('check_response.html.twig', ['success' => false, 'warning' => true, 'ticket' => $ticket]);
        } else {
            $html = $this->renderView('check_response.html.twig', ['success' => true, 'warning' => false, 'ticket' => $ticket]);
        }

        $ticket->setTimesUsed($ticket->getTimesUsed() + 1);
        $entityManager->persist($ticket);
        $entityManager->flush();

        return new JsonResponse(['html' => $html]);
    }
}
