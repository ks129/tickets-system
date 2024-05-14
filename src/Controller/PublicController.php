<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Ticket;
use App\Form\EventType;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicController extends AbstractController
{
    #[Route('/view/{id}', name: 'app_view_event_public')]
    public function viewEvent(Event $event): Response
    {
        if (!$event->isPublic()) {
            throw $this->createNotFoundException();
        }

        return $this->render('view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/buy/{id}', name: 'app_buy_tickets')]
    public function buyTickets(Event $event, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$event->isPublic()) {
            throw $this->createNotFoundException();
        }

        if (!$event->isTicketsAvailable()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TicketType::class, null, ['event' => $event->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setTicketNumber('EVENT-'.$event->getId().'-'. time().'-'.Uuid::uuid4());
            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('Ticket successfully generated. PDF file will be downloaded shortly.'));
            return $this->redirectToRoute('app_view_event_public', ['id' => $event->getId()]);
        }

        return $this->render('buy.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ticket/{ticketNumber}', name: 'app_download_ticket')]
    public function viewTicketPdf(Ticket $ticket, Pdf $knpSnappyPdf): Response
    {
        $knpSnappyPdf->setOption('encoding', 'utf-8');
        $html = $this->renderView('ticket.html.twig', [
            'ticket' => $ticket,
        ]);
        return new PdfResponse($knpSnappyPdf->getOutputFromHtml($html), $ticket->getId().'.pdf');
    }
}
