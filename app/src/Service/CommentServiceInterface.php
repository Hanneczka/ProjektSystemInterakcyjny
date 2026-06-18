<?php

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\Comment;
use App\Entity\Element;

interface CommentServiceInterface
{
    public function getPaginatedList(int $page, Element $element): PaginationInterface;

    public function findByElement(Element $element): array;

    public function delete(Comment $comment): void;
}
