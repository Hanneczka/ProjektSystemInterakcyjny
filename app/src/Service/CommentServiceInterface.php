<?php

/**
 * Comment service interface.
 */

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\Comment;
use App\Entity\Element;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int     $page    Page number
     * @param Element $element Element entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, Element $element): PaginationInterface;

    /**
     * Find comments by element.
     *
     * @param Element $element Element entity
     *
     * @return array<int, Comment> Array of comments
     */
    public function findByElement(Element $element): array;

    /**
     * Delete comment.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;

    /**
     * Save comment.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;
}
