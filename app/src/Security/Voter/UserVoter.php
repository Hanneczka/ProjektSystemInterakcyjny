<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

final class UserVoter extends Voter
{
    public const PASSWORD = 'CHANGE_PASSWORD_USER';

    public const CHANGE = 'CHANGE_USER';

    public const BLOCK = 'BLOCK_USER';
    public const ROLES = 'CHANGE_ROLES_USER';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [

                self::CHANGE,
                self::PASSWORD,
                self::BLOCK,
                self::ROLES
            ]) && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var User $subject */
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

    private function isAdmin(?UserInterface $user): bool
    {
        if (!$user) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
