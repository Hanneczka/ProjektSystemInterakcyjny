<?php

/**
 * Element fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Element;
use App\Entity\Tag;
use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class ElementFixtures.
 */
class ElementFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(100, 'element', function (int $i) {
            $element = new Element();
            $element->setTitle($this->faker->words(3, true));
            $element->setAuthor($this->faker->name);
            $element->setYear($this->faker->year);
            $element->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $element->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $category = $this->getRandomReference('category', Category::class);
            $element->setCategory($category);

            $randomTags = $this->getRandomReferenceList('tag', Tag::class, random_int(1, 3));

            foreach ($randomTags as $tag) {
                $element->addTag($tag);
            }

            return $element;
        });
    }

    /**
     * Get dependencies.
     *
     * @return array<int, string> List of fixture dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class];
    }
}
