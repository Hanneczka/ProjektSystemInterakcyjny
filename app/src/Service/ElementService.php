<?php

/**
 * Element service.
 */

namespace App\Service;

use App\Entity\Element;
use App\Repository\ElementRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Category;
use App\Dto\ElementListInputFiltersDto;
use App\Dto\ElementListFiltersDto;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\CommentRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CoverRepository;
use App\Entity\Cover;

/**
 * Class ElementService.
 */
class ElementService implements ElementServiceInterface
{
    /**
     * Items per page.
     *
     * @var int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ElementRepository        $elementRepository Element repository
     * @param RatingRepository         $ratingRepository  Rating repository
     * @param PaginatorInterface       $paginator         Paginator
     * @param CommentRepository        $commentRepository Comment repository
     * @param CategoryServiceInterface $categoryService   Category service
     * @param TagServiceInterface      $tagService        Tag service
     * @param EntityManagerInterface   $entityManager     Entity manager
     * @param CoverRepository          $coverRepository   Cover repository
     *
     * @return void
     */
    public function __construct(private readonly ElementRepository $elementRepository, private readonly RatingRepository $ratingRepository, private readonly PaginatorInterface $paginator, private readonly CommentRepository $commentRepository, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly EntityManagerInterface $entityManager, private readonly CoverRepository $coverRepository,)
    {
    }

    /**
     * Get paginated list of elements.
     *
     * @param int                        $page    Page number
     * @param ElementListInputFiltersDto $filters Input filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, ElementListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->elementRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['element.id', 'element.createdAt', 'element.updatedAt', 'element.title', 'element.author', 'element.year', 'category.name'],
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save element.
     *
     * @param Element $element Element entity
     *
     * @return void
     */
    public function save(Element $element): void
    {
        $this->elementRepository->save($element);
    }

    /**
     * Delete element along with its comments and ratings.
     *
     * @param Element $element Element entity
     *
     * @return void
     */
    public function delete(Element $element): void
    {
        $comments = $this->commentRepository->findBy(['element' => $element]);
        foreach ($comments as $comment) {
            $this->commentRepository->delete($comment);
        }

        $ratings = $this->ratingRepository->findBy(['element' => $element]);
        foreach ($ratings as $rating) {
            $this->ratingRepository->delete($rating);
        }

        $cover = $this->coverRepository->findOneBy(['element' => $element]);
        if ($cover) {
            $this->coverRepository->delete($cover);
        }
        $this->elementRepository->delete($element);
    }

    /**
     * Get paginated list of elements filtered by category.
     *
     * @param int      $page     Page number
     * @param Category $category Category entity
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->elementRepository->queryByCategory($category),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Find user rating for given element.
     *
     * @param Element       $element Element entity
     * @param UserInterface $user    User entity
     *
     * @return object|null Rating object or null
     */
    public function findUserRating(Element $element, UserInterface $user): ?object
    {
        return $this->ratingRepository->findOneBy([
            'element' => $element,
            'user' => $user,
        ]);
    }

    /**
     * Get average rating for element.
     *
     * @param Element $element Element entity
     *
     * @return float Average rating
     */
    public function getAverageRating(Element $element): float
    {
        $average = $this->ratingRepository->getAverageRatingForElement($element);

        return (float) $average;
    }

    /**
     * Toggle favorite status of given element for current user.
     *
     * @param Element       $element Element entity
     * @param UserInterface $user    User entity
     *
     * @return string Message translation key
     */
    public function toggleFavorite(Element $element, UserInterface $user): string
    {
        if ($user->getFavorites()->contains($element)) {
            $user->removeFavorite($element);
            $messageKey = 'message.deleted_from_favorites';
        } else {
            $user->addFavorite($element);
            $messageKey = 'message.added_to_favorites';
        }

        $this->entityManager->flush();

        return $messageKey;
    }

    /**
     * Prepare filters for the elements list.
     *
     * @param ElementListInputFiltersDto $filters Raw filters from request
     *
     * @return ElementListFiltersDto Result filters
     */
    private function prepareFilters(ElementListInputFiltersDto $filters): ElementListFiltersDto
    {
        return new ElementListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
        );
    }

    /**
     * Find cover for given element.
     *
     * @param Element $element Element entity
     *
     * @return Cover|null Cover entity or null
     */
    public function findCoverForElement(Element $element): ?Cover
    {
        return $this->coverRepository->findOneBy(['element' => $element]);
    }
}
