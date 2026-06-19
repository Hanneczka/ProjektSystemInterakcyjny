<?php

/**
 * Rating repository.
 */

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Element;

/**
 * Class RatingRepository.
 *
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     *
     * @return void
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * Get average rating for a specific element.
     *
     * @param Element $element Element entity
     *
     * @return float|null Average rating or null
     */
    public function getAverageRatingForElement(Element $element): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.value)')
            ->where('r.element = :element')
            ->setParameter('element', $element)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     *
     * @return void
     */
    public function save(Rating $rating): void
    {
        $this->getEntityManager()->persist($rating);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating entity
     *
     * @return void
     */
    public function delete(Rating $rating): void
    {
        $this->getEntityManager()->remove($rating);
        $this->getEntityManager()->flush();
    }
}
