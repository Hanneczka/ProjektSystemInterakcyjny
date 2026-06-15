<?php

namespace App\Service;

use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Element;
use App\Entity\Comment;

class CommentService implements CommentServiceInterface{
    public function __construct(private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }
    private const PAGINATOR_ITEMS_PER_PAGE = 5;
    public function getPaginatedList(int $page, Element $element): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->queryByElement($element),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['comment.id', 'comment.author', 'comment.updatedAt'],
                'defaultSortFieldName' => 'comment.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }
    public function findByElement(Element $element): array
    {
        return $this->commentRepository->findByElement($element);
    }
}
