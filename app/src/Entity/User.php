<?php

/**
 * User entity.
 */

namespace App\Entity;

use App\Entity\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Email.
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * Roles.
     *
     * @var list<int, string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * Hashed password.
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private ?string $password = null;

    /**
     * Name.
     */
    #[ORM\Column(length: 64)]
    private ?string $name = null;

    /**
     * Favorites collection.
     *
     * @var Collection<int, Element>
     */
    #[ORM\ManyToMany(targetEntity: Element::class)]
    private Collection $favorites;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for email.
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter for email.
     *
     * @param string|null $email Email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @return string User identifier
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Getter for roles.
     *
     * @return list<string> Roles list
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * Setter for roles.
     *
     * @param list<int, string> $roles Roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Getter for password.
     *
     * @return string|null Hashed password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Setter for password.
     *
     * @param string|null $password User password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * Removes sensitive information from the token.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Getter for name.
     *
     * @return string|null Name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string|null $name Name
     *
     * @return $this
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for favorites.
     *
     * @return Collection<int, Element> Favorites collection
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    /**
     * Add element to favorites.
     *
     * @param Element $favorite Element entity
     *
     * @return $this
     */
    public function addFavorite(Element $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
        }

        return $this;
    }

    /**
     * Remove element from favorites.
     *
     * @param Element $favorite Element entity
     *
     * @return $this
     */
    public function removeFavorite(Element $favorite): static
    {
        $this->favorites->removeElement($favorite);

        return $this;
    }
}
