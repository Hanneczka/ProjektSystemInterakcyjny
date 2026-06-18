<?php

/**
 * Element entity.
 */

namespace App\Entity;

use App\Repository\ElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Element.
 */
#[ORM\Entity(repositoryClass: ElementRepository::class)]
class Element
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Created at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Title.
     */
    #[ORM\Column(length: 80)]
    private ?string $title = null;

    /**
     * Category.
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    private ?Category $category = null;

    /**
     * Author.
     */
    #[ORM\Column(length: 80, nullable: true)]
    private ?string $author = null;

    /**
     * Year.
     */
    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    /**
     * Tags.
     *
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    /**
     * Cover.
     */
    #[ORM\OneToOne(mappedBy: 'element', cascade: ['persist', 'remove'])]
    private ?Cover $cover = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Getter for Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for created at.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable|null $createdAt Created at
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for updated at.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Getter for title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter for category.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for author.
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param string|null $author Author
     */
    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for year.
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Setter for year.
     *
     * @param int|null $year Year
     */
    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Getter for tags.
     *
     * @return Collection<int, Tag> Tags collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag to collection.
     *
     * @param Tag $tag Tag entity
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Remove tag from collection.
     *
     * @param Tag $tag Tag entity
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Getter for cover.
     */
    public function getCover(): ?Cover
    {
        return $this->cover;
    }

    /**
     * Setter for cover.
     *
     * @param Cover|null $cover Cover
     */
    public function setCover(?Cover $cover): static
    {
        // set the owning side of the relation if necessary
        if ($cover->getElement() !== $this) {
            $cover->setElement($this);
        }

        $this->cover = $cover;

        return $this;
    }
}
