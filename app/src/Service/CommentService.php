<?php

/**
 * Comment service.
 */

namespace App\Service;

use App\Repository\CommentRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Element;
use App\Entity\Comment;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * Items per page.
     *
     * @var int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     *
     * @return void
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int     $page    Page number
     * @param Element $element Element entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
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

    /**
     * Find comments by element.
     *
     * @param Element $element Element entity
     *
     * @return array<int, Comment> Array of comments
     */
    public function findByElement(Element $element): array
    {
        return $this->commentRepository->findByElement($element);
    }

    /**
     * Delete comment.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Save comment.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }
}
