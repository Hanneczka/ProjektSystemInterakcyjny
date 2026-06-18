<?php

/**
 * User voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

/**
 * Class UserVoter.
 */
final class UserVoter extends Voter
{
    /**
     * Change password permission.
     */
    public const PASSWORD = 'CHANGE_PASSWORD_USER';

    /**
     * Change user data permission.
     */
    public const CHANGE = 'CHANGE_USER';

    /**
     * Block user permission.
     */
    public const BLOCK = 'BLOCK_USER';

    /**
     * Change user roles permission.
     */
    public const ROLES = 'CHANGE_ROLES_USER';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::CHANGE,
                self::PASSWORD,
                self::BLOCK,
                self::ROLES,
            ]) && $subject instanceof User;
    }

    /**
     * Perform a single grant vote on a given attribute, subject and token.
     *
     * @param string         $attribute Attribute
     * @param mixed          $subject   Subject (expected User instance)
     * @param TokenInterface $token     Token
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PASSWORD:
            case self::CHANGE:
                return $this->isAdmin($user);

            case self::BLOCK:
            case self::ROLES:
                return $this->isAdmin($user) && $user !== $subject;
        }

        return false;
    }

    /**
     * Check if user has admin role.
     *
     * @param UserInterface|null $user User entity
     */
    private function isAdmin(?UserInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
