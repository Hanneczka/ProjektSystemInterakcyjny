<?php

/**
 * User service.
 */

namespace App\Service;

use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Items per page for users list.
     *
     * @var int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Items per page for favorites list.
     *
     * @var int
     */
    private const PAGINATOR_FAVORITES_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository UserRepository
     * @param PaginatorInterface          $paginator      Paginator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     *
     * @return void
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['user.id', 'user.name', 'user.email'],
                'defaultSortFieldName' => 'user.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Delete user.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        $this->userRepository->delete($user);
    }

    /**
     * Get paginated list of user favorites.
     *
     * @param User $user User entity
     * @param int  $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedFavorites(User $user, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $user->getFavorites(),
            $page,
            self::PAGINATOR_FAVORITES_PER_PAGE
        );
    }

    /**
     * Upgrade password.
     *
     * @param User   $user        User entity
     * @param string $newPassword New plain password
     */
    public function upgradePassword(User $user, string $newPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $this->save($user);
    }

    /**
     * Check if given user is the last admin in system.
     *
     * @param User $user User entity
     *
     * @return bool True if last admin, false otherwise
     */
    public function isLastAdmin(User $user): bool
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return false;
        }

        return $this->userRepository->countAdmins() <= 1;
    }

    /**
     * Toggle admin role for given user.
     *
     * @param User $user User entity
     */
    public function toggleAdminRole(User $user): void
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
        } else {
            $roles[] = 'ROLE_ADMIN';
        }

        $user->setRoles(array_values(array_unique($roles)));
        $this->save($user);
    }

    /**
     * Toggle block user status.
     *
     * @param User $user User entity
     */
    public function toggleBlockUser(User $user): void
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_BLOCKED', $roles, true)) {
            $roles = array_diff($roles, ['ROLE_BLOCKED']);
        } else {
            $roles[] = 'ROLE_BLOCKED';
        }

        $user->setRoles(array_values(array_unique($roles)));
        $this->save($user);
    }
}
