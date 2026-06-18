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
     */
    public function loadData(): void
    {
        $this->createMany(500, 'Ratings', function (int $i) {
            $Rating = new Rating();

            $Rating->setValue($this->faker->numberBetween(1, 5));

            $Rating->setUser($this->getRandomReference('user', User::class));
            $Rating->setElement($this->getRandomReference('element', Element::class));

            return $Rating;
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
