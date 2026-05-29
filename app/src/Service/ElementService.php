<?php

namespace App\Service;

use App\Entity\Element;
use App\Repository\ElementRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ElementService implements ElementServiceInterface{

    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    public function __construct(private readonly ElementRepository $elementRepository, private readonly PaginatorInterface $paginator) {
    }
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->elementRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['element.id', 'element.createdAt', 'element.updatedAt', 'element.title', 'element.author', 'element.year', 'element.tag.title', 'element.category'],
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }
    public function save(Element $element): void
    {
        $element->setUpdatedAt(new \DateTimeImmutable());
        if (null === $element->getId()) {
            $element->setCreatedAt(new \DateTimeImmutable());
        }
        $this->elementRepository->save($element);
    }
    public function delete(Element $element): void
    {
        $this->elementRepository->delete($element);
    }

}
