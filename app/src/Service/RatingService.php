<?php

namespace App\Service;

use App\Entity\Rating;
use App\Repository\RatingRepository;

class RatingService implements RatingServiceInterface{
    public function __construct(
        private readonly RatingRepository $ratingRepository
    ) {
    }
    public function save(Rating $rating): void
    {

        $this->ratingRepository->save($rating);
    }

    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

}
