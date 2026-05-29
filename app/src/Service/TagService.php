<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TagService implements TagServiceInterface{
public function __construct(private readonly TagRepository $tagRepository, private readonly PaginatorInterface $paginator) {}
    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->tagRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['tag.id', 'tag.createdAt', 'tag.updatedAt', 'tag.titile'],
                'defaultSortFieldName' => 'tag.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }
    public function save(Tag $tag): void
    {
        $tag->setUpdatedAt(new \DateTimeImmutable());
        if (null === $tag->getId()) {
            $tag->setCreatedAt(new \DateTimeImmutable());
        }
        $this->tagRepository->save($tag);
    }
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }
    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }
    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }
}
