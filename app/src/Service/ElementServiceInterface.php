<?php

namespace App\Service;

use App\Entity\Element;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface ElementServiceInterface {

    public function getPaginatedList(int $page): PaginationInterface;
public function save(Element $element): void;
public function delete(Element $element): void;
}
