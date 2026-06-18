<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Element;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Tag;
use App\Dto\ElementListFiltersDto;

/**
 * @extends ServiceEntityRepository<Element>
 */
class ElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Element::class);
    }

    public function queryAll(ElementListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('element')
            ->select(
                'partial element.{id, createdAt, updatedAt, title, year, author}',
                'partial category.{id, name}',
                'partial tags.{id, title}'
            )
            ->join('element.category', 'category')
            ->leftJoin('element.tags', 'tags');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    public function save(Element $element): void
    {
        $this->getEntityManager()->persist($element);
        $this->getEntityManager()->flush();
    }
    public const PAGINATOR_ITEMS_PER_PAGE = 3;

    public function delete(Element $element): void
    {
        $this->getEntityManager()->remove($element);
        $this->getEntityManager()->flush();
    }

    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('element.id'))
            ->where('element.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('element');
    }

    public function queryByCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('element')
            ->where('element.category = :category')
            ->setParameter('category', $category)
            ->orderBy('element.createdAt', 'DESC');
    }

    private function applyFiltersToList(QueryBuilder $queryBuilder, ElementListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }


        return $queryBuilder;
    }

    public function getHighestRated(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->select('e, AVG(r.value) as avg_rating')
            ->leftJoin('App\Entity\Rating', 'r', 'WITH', 'r.element = e')
            ->groupBy('e.id')
            ->orderBy('avg_rating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Element[] Returns an array of Element objects
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

    //    public function findOneBySomeField($value): ?Element
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
