<?php

namespace App\Service;

use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements UserServiceInterface
{
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly EntityManagerInterface $entityManager)
    {
    }
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    private const PAGINATOR_FAVORITES_PER_PAGE = 5;

    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['user.id', 'user.name', 'user.email'],
                'defaultSortFieldName' => 'user.id',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    public function save(User $user): void
    {

        $this->userRepository->save($user);
    }

    public function delete(User $user): void
    {
        $this->userRepository->delete($user);
    }

    public function getPaginatedFavorites(User $user, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $user->getFavorites(),
            $page,
            self::PAGINATOR_FAVORITES_PER_PAGE,
        );
    }
}
