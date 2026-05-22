<?php
namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
class CategoryRepository extends ServiceEntityRepository{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('category');}

    public function save(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }
    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }
    public const PAGINATOR_ITEMS_PER_PAGE = 3;
}
