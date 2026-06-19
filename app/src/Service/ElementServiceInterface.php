<?php

/**
 * Element service interface.
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\Element;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Dto\ElementListInputFiltersDto;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Cover;

/**
 * Interface ElementServiceInterface.
 */
interface ElementServiceInterface
{
    /**
     * Get paginated list of elements.
     *
     * @param int                        $page    Page number
     * @param ElementListInputFiltersDto $filters Input filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, ElementListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save element.
     *
     * @param Element $element Element entity
     */
    public function save(Element $element): void;

    /**
     * Delete element.
     *
     * @param Element $element Element entity
     */
    public function delete(Element $element): void;

    /**
     * Get paginated list of elements filtered by category.
     *
     * @param int      $page     Page number
     * @param Category $category Category entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface;

    /**
     * Find user rating for given element.
     *
     * @param Element       $element Element entity
     * @param UserInterface $user    User entity
     *
     * @return object|null Rating object or null
     */
    public function findUserRating(Element $element, UserInterface $user): ?object;

    /**
     * Get average rating for element.
     *
     * @param Element $element Element entity
     *
     * @return float Average rating
     */
    public function getAverageRating(Element $element): float;

    /**
     * Toggle favorite status of given element for current user.
     *
     * @param Element       $element Element entity
     * @param UserInterface $user    User entity
     *
     * @return string Message translation key
     */
    public function toggleFavorite(Element $element, UserInterface $user): string;

    /**
     * Find cover for given element.
     *
     * @param Element $element Element entity
     *
     * @return Cover|null Cover entity or null
     */
    public function findCoverForElement(Element $element): ?Cover;
}
