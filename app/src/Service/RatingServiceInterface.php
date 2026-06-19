<?php

/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Element;
use App\Entity\Rating;
use App\Entity\User;

/**
 * Interface RatingServiceInterface.
 */
interface RatingServiceInterface
{
    /**
     * Save rating.
     *
     * @param Rating $rating Rating entity
     */
    public function save(Rating $rating): void;

    /**
     * Delete rating.
     *
     * @param Rating $rating Rating entity
     */
    public function delete(Rating $rating): void;

    /**
     * Find user rating for element.
     *
     * @param Element $element Element entity
     * @param User    $user    User entity
     *
     * @return Rating|null Rating entity or null
     */
    public function findUserRatingForElement(Element $element, User $user): ?Rating;

    /**
     * Get highest rated elements.
     *
     * @param int $limit Limit
     *
     * @return array Result array
     */
    public function getHighestRatedElements(int $limit = 10): array;
}
