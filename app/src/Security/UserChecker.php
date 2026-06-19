<?php

/**
 * User checker.
 */

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserChecker.
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user User entity
     *
     * @throws CustomUserMessageAuthenticationException If the user is blocked
     */
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

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user User entity
     */
    public function checkPostAuth(UserInterface $user): void
    {
    }
}
