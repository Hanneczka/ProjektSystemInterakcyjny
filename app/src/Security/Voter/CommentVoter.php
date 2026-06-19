<?php

/**
 * Comment voter.
 */

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
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
     *
     * @param AuthorizationCheckerInterface $security Authorization checker
     *
     * @return void
     */
    public function __construct(private readonly Security $security)
    {
    }

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
        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof Comment;
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
     * @param Comment       $comment Comment entity
     * @param UserInterface $user    User entity
     *
     * @return bool True if user is author, false otherwise
     */
    private function isAuthor(Comment $comment, UserInterface $user): bool
    {
        return $comment->getAuthor() && $comment->getAuthor()->getId() === $user->getId();
    }
}
