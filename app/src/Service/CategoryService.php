<?php

/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ElementRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Items per page.
     *
     * @var int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param ElementRepository  $elementRepository  Element repository
     * @param PaginatorInterface $paginator          Paginator
     *
     * @return void
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly ElementRepository $elementRepository, private readonly PaginatorInterface $paginator,)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['category.id', 'category.createdAt', 'category.updatedAt', 'category.name'],
                'defaultSortFieldName' => 'category.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save category.
     *
     * @param Category $category Category entity
     *
     * @return void
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete category.
     *
     * @param Category $category Category entity
     *
     * @return void
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can category be deleted.
     *
     * @param Category $category Category entity
     *
     * @return bool True if category can be deleted, false otherwise
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->elementRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Find by id.
     *
     * @param int $id Category id
     *
     * @return Category|null Category entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->findOneById($id);
    }
}
