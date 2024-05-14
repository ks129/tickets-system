<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findEventsForAdminQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.id', 'ASC');
    }

    public function findEventsForUserQueryBuilder(?User $user): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->andWhere(':user MEMBER OF e.hosts OR :user MEMBER OF e.ticketCheckers')
            ->setParameter('user', $user->getId())
            ->orderBy('e.id', 'ASC');
    }

    public function findCurrentPublicEvents(): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isPublic = true')
            ->andWhere('e.endAt >= CURRENT_TIMESTAMP()')
            ->orderBy('e.beginAt', 'ASC');
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
