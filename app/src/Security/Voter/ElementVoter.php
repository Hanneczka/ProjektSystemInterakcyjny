<?php

/**
 * Element voter.
 */

namespace App\Security\Voter;

use App\Entity\Element;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ElementVoter.
 */
final class ElementVoter extends Voter
{
    /**
     * Delete permission.
     */
    public const DELETE = 'ELEMENT_DELETE';

    /**
     * Edit permission.
     */
    public const EDIT = 'ELEMENT_EDIT';

    /**
     * Show permission.
     */
    public const VIEW = 'ELEMENT_VIEW';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     *
     * @return bool True if supported, false otherwise
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Element;
    }

    /**
     * Perform a single grant vote on a given attribute, subject and token.
     *
     * @param string         $attribute Attribute
     * @param mixed          $subject   Subject
     * @param TokenInterface $token     Token
     * @param Vote|null      $vote      Vote object
     *
     * @return bool True if permission is granted, false otherwise
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::EDIT, self::DELETE => $this->isAdmin($user),
            self::VIEW => true,
            default => false,
        };
    }

    /**
     * Check if user has admin role.
     *
     * @param UserInterface|null $user User entity
     *
     * @return bool True if user is admin, false otherwise
     */
    private function isAdmin(?UserInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
