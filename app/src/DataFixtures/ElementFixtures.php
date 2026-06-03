<?php

namespace App\DataFixtures;

use App\Entity\Element;
use App\Entity\Tag;
use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class ElementFixtures extends AbstractBaseFixtures implements DependentFixtureInterface{
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
            $tag = $this->getRandomReference('tag', Tag::class);
            $element->addTag($tag);

            return $element;
        });
    }
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class];
    }
}
