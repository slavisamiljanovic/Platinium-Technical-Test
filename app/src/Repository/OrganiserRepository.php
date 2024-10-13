<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Organiser;

/**
 * @extends ServiceEntityRepository<Organiser>
 *
 * @method Organiser|null find($id, $lockMode = null, $lockVersion = null)
 */
class OrganiserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organiser::class);
    }

    private function createOrganisersQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->from(Organiser::class, 'o');
        return $queryBuilder;
    }

    public function save(Organiser $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Organiser $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function countOrganisers(): int
    {
        $queryBuilder = $this->createOrganisersQueryBuilder();
        $queryBuilder->select('COUNT(o)');
        /** @var int */
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return Organiser[]
     */
    public function findOrganisers(int $limit, int $offset): array
    {
        $queryBuilder = $this->createOrganisersQueryBuilder();
        $queryBuilder->select('o');
        $queryBuilder->orderBy('o.createdAt', 'DESC');
        $queryBuilder->setFirstResult($offset);
        $queryBuilder->setMaxResults($limit);
        /** @var Organiser[] */
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    //    /**
    //     * @return Organiser[] Returns an array of Organiser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Organiser
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
