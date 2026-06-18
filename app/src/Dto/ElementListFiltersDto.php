<?php

/**
 * Element list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Tag;

/**
 * Class ElementListFiltersDto.
 */
class ElementListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null $category Category entity
     * @param Tag|null      $tag      Tag entity
     */
    public function __construct(public readonly ?Category $category, public readonly ?Tag $tag)
    {
    }
}
