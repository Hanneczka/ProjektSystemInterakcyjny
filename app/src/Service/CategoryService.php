<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
class CategoryService implements CategoryServiceInterface{
    public function __construct(private readonly CategoryRepository $categoryRepository) {
    }
    public function save(Category $category): void
    {
        $category->setUpdatedAt(new \DateTimeImmutable());
        if (null === $category->getId()) {
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $this->categoryRepository->save($category);
    }

}
