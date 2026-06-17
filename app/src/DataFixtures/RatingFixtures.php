<?php

namespace App\DataFixtures;

use App\Entity\Rating;
use App\Entity\Element;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RatingFixtures extends AbstractBaseFixtures implements DependentFixtureInterface{
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
    public function getDependencies(): array
    {
        return [ElementFixtures::class, UserFixtures::class];
    }

}
