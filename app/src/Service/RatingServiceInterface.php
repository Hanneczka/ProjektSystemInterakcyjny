<?php

/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Rating;

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
}
