<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const DELETE = 'COMMENT_DELETE';
    public const EDIT = 'COMMENT_EDIT';

    public function __construct(
        private readonly AuthorizationCheckerInterface $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof \App\Entity\Comment;
    }


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


    private function isAuthor(Comment $comment, User $user): bool
    {
        return $comment->getAuthor() && $comment->getAuthor()->getId() === $user->getId();
    }
}
