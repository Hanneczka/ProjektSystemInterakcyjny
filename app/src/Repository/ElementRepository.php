<?php

/**
 * Element repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Element;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Tag;
use App\Dto\ElementListFiltersDto;

/**
 * Class ElementRepository.
 *
 * @extends ServiceEntityRepository<Element>
 */
class ElementRepository extends ServiceEntityRepository
{
    /**
     * Number of items per page in paginator.
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 3;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     *
     * @return void
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Element::class);
    }

    /**
     * Query all records with filters applied.
     *
     * @param ElementListFiltersDto $filters Filters DTO
     *
     * @return QueryBuilder Query builder
     */
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

    /**
     * Save entity.
     *
     * @param Element $element Element entity
     *
     * @return void
     */
    public function save(Element $element): void
    {
        $this->getEntityManager()->persist($element);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Element $element Element entity
     *
     * @return void
     */
    public function delete(Element $element): void
    {
        $this->getEntityManager()->remove($element);
        $this->getEntityManager()->flush();
    }

    /**
     * Count elements by category.
     *
     * @param Category $category Category entity
     *
     * @return int Number of tasks in category
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('element.id'))
            ->where('element.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get or create a query builder instance.
     *
     * @param QueryBuilder|null $qb Query builder
     *
     * @return QueryBuilder Query builder
     */
    public function getOrCreateQueryBuilder(?QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('element');
    }

    /**
     * Query elements by category.
     *
     * @param Category $category Category entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('element')
            ->where('element.category = :category')
            ->setParameter('category', $category)
            ->orderBy('element.createdAt', 'DESC');
    }

    /**
     * Get highest rated elements.
     *
     * @param int $limit Results limit
     *
     * @return array<int, mixed> List of elements with ratings
     */
    public function getHighestRated(int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->select('e, AVG(r.value) as avg_rating')
            ->leftJoin('App\Entity\Rating', 'r', 'ON', 'r.element = e')
            ->groupBy('e.id')
            ->orderBy('avg_rating', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Apply filters to element list.
     *
     * @param QueryBuilder          $queryBuilder Query builder
     * @param ElementListFiltersDto $filters      Filters DTO
     *
     * @return QueryBuilder Query builder
     */
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
}
