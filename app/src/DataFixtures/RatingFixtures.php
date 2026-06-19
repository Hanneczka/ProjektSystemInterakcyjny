<?php

/**
 * Rating fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Rating;
use App\Entity\Element;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class RatingFixtures.
 */
class RatingFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @return void
     */
    public function loadData(): void
    {
        $this->createMany(500, 'Ratings', function (int $i) {
            $rating = new Rating();

            $rating->setValue($this->faker->numberBetween(1, 5));

            $rating->setUser($this->getRandomReference('user', User::class));
            $rating->setElement($this->getRandomReference('element', Element::class));

            return $rating;
        });

        $this->manager->flush();
    }

    /**
     * Get dependencies.
     *
     * @return array<int, string> List of fixture dependencies
     */
    public function getDependencies(): array
    {
        return [ElementFixtures::class, UserFixtures::class];
    }
}
