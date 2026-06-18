<?php

/**
 * Cover entity.
 */

namespace App\Entity;

use App\Repository\CoverRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Cover.
 */
#[ORM\Entity(repositoryClass: CoverRepository::class)]
class Cover
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Element.
     */
    #[ORM\OneToOne(inversedBy: 'cover', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $element = null;

    /**
     * File name.
     */
    #[ORM\Column(length: 191)]
    private ?string $fileName = null;

    /**
     * Getter for Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for element.
     */
    public function getElement(): ?Element
    {
        return $this->element;
    }

    /**
     * Setter for element.
     *
     * @param Element|null $element Element
     */
    public function setElement(?Element $element): static
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Getter for file name.
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * Setter for file name.
     *
     * @param string|null $fileName File name
     */
    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }
}
