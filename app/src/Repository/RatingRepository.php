<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Element;

/**
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function getAverageRatingForElement(Element $element): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.value)')
            ->where('r.element = :element')
            ->setParameter('element', $element)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function save(Rating $rating): void
    {
        $this->getEntityManager()->persist($rating);
        $this->getEntityManager()->flush();
    }

    public function delete(Rating $rating): void
    {
        $this->getEntityManager()->remove($rating);
        $this->getEntityManager()->flush();
    }


    //    /**
    //     * @return Rating[] Returns an array of Rating objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rating
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
