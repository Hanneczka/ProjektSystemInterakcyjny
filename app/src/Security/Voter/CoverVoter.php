<?php

/**
 * Cover voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CoverVoter.
 */
final class CoverVoter extends Voter
{
    /**
     * Edit permission.
     */
    public const EDIT = 'COVER_EDIT';

    /**
     * Delete permission.
     */
    public const DELETE = 'COVER_DELETE';

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
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Cover;
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

        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                return $this->isAdmin($user);
        }

        return false;
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
