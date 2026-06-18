<?php

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\User;

interface UserServiceInterface
{
    public function getPaginatedList(int $page): PaginationInterface;
    public function save(User $user): void;

    public function delete(User $user): void;

    public function getPaginatedFavorites(User $user, int $page): PaginationInterface;
}
