<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Organiser;
use App\Entity\Favorite;
use App\Entity\Follower;
use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organiser>
 *
 * @method Organiser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organiser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organiser|null findOneBySlug(string $slug)
 * @method Organiser[]    findAll()
 * @method Organiser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganiserRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organiser::class);
    }

    private function createOrganisersFeedQueryBuilder(Profile $follower): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->from(Organiser::class, 'a')
            ->join(
                Follower::class,
                'f',
                Join::WITH,
                $queryBuilder->expr()->andX(
                    'a.author = f.profile',
                    $queryBuilder->expr()->eq('f.follower', ':follower_id')
                )
            )
            ->setParameter('follower_id', $follower->getId());
        return $queryBuilder;
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

    public function countOrganisersFeed(Profile $follower): int
    {
        $queryBuilder = $this->createOrganisersFeedQueryBuilder($follower);
        $queryBuilder->select('COUNT(a)');
        /** @var int */
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        return $result;
    }

    /**
     * @param Profile $follower
     * @param integer $limit
     * @param integer $offset
     * @return Organiser[]
     */
    public function findOrganisersFeed(Profile $follower, int $limit, int $offset): array
    {
        $queryBuilder = $this->createOrganisersFeedQueryBuilder($follower);
        $queryBuilder->select('a');
        $queryBuilder->orderBy('a.createdAt', 'DESC');
        $queryBuilder->setFirstResult($offset);
        $queryBuilder->setMaxResults($limit);
        /** @var Organiser[] */
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
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
     * @param string|null $author
     * @param string|null $favorited
     * @param string|null $tag
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
