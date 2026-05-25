<?php

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

class TagService implements TagServiceInterface{
public function __construct(private readonly TagRepository $tagRepository) {}

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
}
