<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TagVoter extends Voter
{
    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'TAG_DELETE';

    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'TAG_EDIT';

    /**
     * Show permission.
     *
     * @const string
     */
    public const VIEW = 'TAG_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Tag;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();


        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::VIEW:

                return $this->isAdmin($user);


                return true;
        }

        return false;
    }
    private function isAdmin(?UserInterface $user): bool
    {
        if (!$user) {
            return false;
        }
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
