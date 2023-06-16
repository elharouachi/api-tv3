<?php

namespace App\Repository;

use App\Entity\BrightcoveSource;
use App\Entity\Movie;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function getMovieCount(): int
    {
        $queryBuilder = $this->getMoviesBuilder();
        $queryBuilder->select('COUNT(m)');
        $query = $queryBuilder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getMoviesNosHasPosters(string $offset, string $limit): array
    {
        $queryBuilder = $this->getMoviesBuilder();
        $queryBuilder->select('m')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    private function getMoviesBuilder()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->from(Movie::class, 'm');
        $queryBuilder->where('m.poster IS NULL');

        return $queryBuilder;
    }
}
