<?php

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;

interface UserServiceInterface {
    public function getPaginatedList(int $page): PaginationInterface;

}
