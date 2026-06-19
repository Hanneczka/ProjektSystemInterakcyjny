<?php

/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Rating value.
     */
    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 5,
    )]
    private ?int $value = null;

    /**
     * Element.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $element = null;

    /**
     * User.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for value.
     *
     * @return int|null Rating value
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Setter for value.
     *
     * @param int|null $value Rating value
     *
     * @return $this
     */
    public function setValue(?int $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Getter for element.
     *
     * @return Element|null Element
     */
    public function getElement(): ?Element
    {
        return $this->element;
    }

    /**
     * Setter for element.
     *
     * @param Element|null $element Element
     *
     * @return $this
     */
    public function setElement(?Element $element): static
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User|null $user User
     *
     * @return $this
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
