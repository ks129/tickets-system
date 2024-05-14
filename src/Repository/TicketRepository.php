<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findAllEventTickets(Event $event): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.ticketType', 'tt')
            ->andWhere('tt.event = :event')
            ->setParameter('event', $event)
            ->orderBy('t.id', 'ASC');
    }

    public function findByEventAndTicketNumber(Event $event, string $ticketNumber): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.ticketType', 'tt')
            ->andWhere('tt.event = :event')
            ->andWhere('t.ticketNumber = :ticketNumber')
            ->setParameter('event', $event)
            ->setParameter('ticketNumber', $ticketNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Ticket[] Returns an array of Ticket objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ticket
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
