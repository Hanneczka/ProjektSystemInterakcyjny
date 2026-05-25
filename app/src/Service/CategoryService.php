<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ElementRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
class CategoryService implements CategoryServiceInterface{
    public function __construct(private readonly CategoryRepository $categoryRepository,
                                private readonly ElementRepository $elementRepository,
                                private readonly PaginatorInterface $paginator) {
    }
    private const PAGINATOR_ITEMS_PER_PAGE = 3;
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
    public function save(Category $category): void
    {
        $category->setUpdatedAt(new \DateTimeImmutable());
        if (null === $category->getId()) {
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $this->categoryRepository->save($category);
    }
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->elementRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }



}
