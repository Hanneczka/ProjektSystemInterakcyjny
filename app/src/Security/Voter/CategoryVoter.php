<?php

/**
 * Category voter.
 */

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Category;

/**
 * Class CategoryVoter.
 */
final class CategoryVoter extends Voter
{
    /**
     * Delete permission.
     */
    public const DELETE = 'CATEGORY_DELETE';

    /**
     * Edit permission.
     */
    public const EDIT = 'CATEGORY_EDIT';

    /**
     * Show permission.
     */
    public const VIEW = 'CATEGORY_VIEW';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Category;
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
                return $this->isAdmin($user);
            case self::VIEW:
                return true;
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
