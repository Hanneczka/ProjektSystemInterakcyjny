<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Element;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Dto\ElementListInputFiltersDto;
use App\Dto\ElementListFiltersDto;



interface ElementServiceInterface {

    public function getPaginatedList(int $page, ElementListInputFiltersDto $filters): PaginationInterface;
public function save(Element $element): void;
public function delete(Element $element): void;
public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;
}
