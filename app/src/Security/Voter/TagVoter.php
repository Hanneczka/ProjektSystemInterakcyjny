<?php

/**
 * Tag voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TagVoter.
 */
final class TagVoter extends Voter
{
    /**
     * Delete permission.
     */
    public const DELETE = 'TAG_DELETE';

    /**
     * Edit permission.
     */
    public const EDIT = 'TAG_EDIT';

    /**
     * Show permission.
     */
    public const VIEW = 'TAG_VIEW';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Tag;
    }

    /**
     * Perform a single grant vote on a given attribute, subject and token.
     *
     * @param string         $attribute Attribute
     * @param mixed          $subject   Subject
     * @param TokenInterface $token     Token
     * @param Vote|null      $vote      Vote object
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::VIEW:
                return $this->isAdmin($user);
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
