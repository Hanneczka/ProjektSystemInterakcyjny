<?php

namespace App\Service;

use App\Entity\Element;
use App\Repository\ElementRepository;

class ElementService implements ElementServiceInterface{
    public function __construct(private readonly ElementRepository $elementRepository) {
    }
    public function save(Element $element): void
    {
        $element->setUpdatedAt(new \DateTimeImmutable());
        if (null === $element->getId()) {
            $element->setCreatedAt(new \DateTimeImmutable());
        }
        $this->elementRepository->save($element);
    }
    public function delete(Element $element): void
    {
        $this->elementRepository->delete($element);
    }
}
