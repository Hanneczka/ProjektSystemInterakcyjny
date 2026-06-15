<?php

namespace App\Service;

use App\Entity\Element;
use App\Repository\ElementRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Category;
use App\Dto\ElementListInputFiltersDto;
use App\Dto\ElementListFiltersDto;


class ElementService implements ElementServiceInterface{

    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    public function __construct(private readonly ElementRepository $elementRepository, private readonly PaginatorInterface $paginator, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService) {
    }
    public function getPaginatedList(int $page, ElementListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);
        return $this->paginator->paginate(
            $this->elementRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['element.id', 'element.createdAt', 'element.updatedAt', 'element.title', 'element.author', 'element.year', 'element.tag.title', 'category.name'],
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }
    public function save(Element $element): void
    {

        $this->elementRepository->save($element);
    }
    public function delete(Element $element): void
    {
        $this->elementRepository->delete($element);
    }
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->elementRepository->queryByCategory($category),
            $page,
            10,
            [
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }
    /**
     * Prepare filters for the elements list.
     *
     * @param ElementListInputFiltersDto $filters Raw filters from request
     *
     * @return ElementListFiltersDto Result filters
     */
    private function prepareFilters(ElementListInputFiltersDto $filters): ElementListFiltersDto
    {
        return new ElementListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,

        );
    }
}
