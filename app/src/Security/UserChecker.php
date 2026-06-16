<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (in_array('ROLE_BLOCKED', $user->getRoles())) {
            $errorMessage = $this->translator->trans('message.account_blocked');
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
