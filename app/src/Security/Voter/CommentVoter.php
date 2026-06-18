<?php

/**
 * Comment voter.
 */

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentVoter.
 */
class CommentVoter extends Voter
{
    /**
     * Delete permission.
     */
    public const DELETE = 'COMMENT_DELETE';

    /**
     * Edit permission.
     */
    public const EDIT = 'COMMENT_EDIT';

    /**
     * Constructor.
     */
    public function __construct(
        private readonly AuthorizationCheckerInterface $security,
    ) {
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof Comment;
    }

    /**
     * Perform a single grant vote on a given attribute, subject and token.
     *
     * @param string         $attribute Attribute
     * @param mixed          $subject   Subject
     * @param TokenInterface $token     Token
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $comment = $subject;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::DELETE, self::EDIT => $this->isAuthor($comment, $user),
            default => false,
        };
    }

    /**
     * Check if user is the author of the comment.
     *
     * @param Comment $comment Comment entity
     * @param User    $user    User entity
     */
    private function isAuthor(Comment $comment, User $user): bool
    {
        return $comment->getAuthor() && $comment->getAuthor()->getId() === $user->getId();
    }
}
