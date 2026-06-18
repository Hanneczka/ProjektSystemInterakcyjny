<?php

namespace App\Service;

use App\Entity\Rating;

interface RatingServiceInterface
{
    public function save(Rating $rating): void;

    public function delete(Rating $rating): void;
}
