<?php

/**
 * User service interface.
 */

namespace App\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Entity\User;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Delete user.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;

    /**
     * Get paginated list of user favorites.
     *
     * @param User $user User entity
     * @param int  $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedFavorites(User $user, int $page): PaginationInterface;

    /**
     * Upgrade password.
     *
     * @param User   $user        User entity
     * @param string $newPassword New plain password
     */
    public function upgradePassword(User $user, string $newPassword): void;

    /**
     * Check if given user is the last admin in system.
     *
     * @param User $user User entity
     *
     * @return bool True if last admin, false otherwise
     */
    public function isLastAdmin(User $user): bool;

    /**
     * Toggle admin role for given user.
     *
     * @param User $user User entity
     */
    public function toggleAdminRole(User $user): void;

    /**
     * Toggle block user status.
     *
     * @param User $user User entity
     */
    public function toggleBlockUser(User $user): void;
}
