<?php

/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Element;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(500, 'comments', function (int $i) {
            $comment = new Comment();

            $comment->setContent($this->faker->text(150));
            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-10 days')
                )
            );
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-10 days', '-1 days')
                )
            );

            $comment->setAuthor($this->getRandomReference('user', User::class));
            $comment->setElement($this->getRandomReference('element', Element::class));

            return $comment;
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
