<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ElementRepository;
class CategoryService implements CategoryServiceInterface{
    public function __construct(private readonly CategoryRepository $categoryRepository,
                                private readonly ElementRepository $elementRepository) {
    }
    public function save(Category $category): void
    {
        $category->setUpdatedAt(new \DateTimeImmutable());
        if (null === $category->getId()) {
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $this->categoryRepository->save($category);
    }
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->elementRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }



}
