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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserChecker.
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     *
     * @return void
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
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
     * @param UserInterface       $user  User entity
     * @param TokenInterface|null $token Token interface
     */
    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
