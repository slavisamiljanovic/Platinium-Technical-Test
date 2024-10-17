<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Event;
use App\Entity\Organiser;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 */
class EventRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    private function createEventsQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->from(Event::class, 'e')
            ->join(Organiser::class, 'o', Join::WITH, 'o.id = e.organiser')
        ;
        return $queryBuilder;
    }

    private function createEventsFeedQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createEventsQueryBuilder();
        return $queryBuilder;
    }

    public function save(Event $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Event $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function countEvents(): int
    {
        $queryBuilder = $this->createEventsQueryBuilder();
        $queryBuilder->select('COUNT(e)');
        /** @var int */
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * @return int
     */
    public function countEventsFeed(): int
    {
        $queryBuilder = $this->createEventsFeedQueryBuilder();
        $queryBuilder->select('COUNT(e)');
        /** @var int */
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * @param  integer $limit
     * @param  integer $offset
     * @return Event[]
     */
    public function findEvents(int $limit, int $offset): array
    {
        $queryBuilder = $this->createEventsQueryBuilder();
        $queryBuilder->select('e');
        $queryBuilder->orderBy('e.createdAt', 'DESC');
        $queryBuilder->setFirstResult($offset);
        $queryBuilder->setMaxResults($limit);
        /** @var Event[] */
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @return Event[]
     */
    public function findEventsFeed(): array
    {
        $queryBuilder = $this->createEventsFeedQueryBuilder();
        $queryBuilder->select('e');
        $queryBuilder->orderBy('e.createdAt', 'DESC');
        /** @var Event[] */
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param  int[]   $events
     * @return Event[]
     */
    public function findEventsBy(array $events): array
    {
        $events = $this->findBy(['id' => $events]);
        return $events;
    }

}
