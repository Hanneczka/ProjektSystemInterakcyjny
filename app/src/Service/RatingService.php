<?php

/**
 * Rating service.
 */

namespace App\Service;

use App\Entity\Element;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\ElementRepository;
use App\Repository\RatingRepository;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Constructor.
     *
     * @param RatingRepository  $ratingRepository  Rating repository
     * @param ElementRepository $elementRepository Element repository
     */
    public function __construct(private readonly RatingRepository $ratingRepository, private readonly ElementRepository $elementRepository)
    {
    }

    /**
     * Save rating.
     *
     * @param Rating $rating Rating entity
     */
    public function save(Rating $rating): void
    {
        $this->ratingRepository->save($rating);
    }

    /**
     * Delete rating.
     *
     * @param Rating $rating Rating entity
     */
    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

    /**
     * Find user rating for element.
     *
     * @param Element $element Element entity
     * @param User    $user    User entity
     *
     * @return Rating|null Rating entity or null
     */
    public function findUserRatingForElement(Element $element, User $user): ?Rating
    {
        return $this->ratingRepository->findOneBy([
            'element' => $element,
            'user' => $user,
        ]);
    }

    /**
     * Get highest rated elements.
     *
     * @param int $limit Limit
     *
     * @return array Result array
     */
    public function getHighestRatedElements(int $limit = 10): array
    {
        return $this->elementRepository->getHighestRated($limit);
    }
}
