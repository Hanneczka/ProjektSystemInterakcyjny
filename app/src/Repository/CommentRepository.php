<?php

/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Element;

/**
 * Class CommentRepository.
 *
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     *
     * @return void
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Query comments by element.
     *
     * @param Element $element Element entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByElement(Element $element): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->where('comment.element = :element')
            ->setParameter('element', $element)
            ->orderBy('comment.createdAt', 'DESC');
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();
    }

    /**
     * Save entity.
     *
     * @param Comment $entity Comment entity
     */
    public function save(Comment $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
