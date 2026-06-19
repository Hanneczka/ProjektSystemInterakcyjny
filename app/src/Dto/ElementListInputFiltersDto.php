<?php

/**
 * Element list input filters DTO.
 */

namespace App\Dto;

/**
 * Class ElementListInputFiltersDto.
 */
class ElementListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null $categoryId Category ID filter
     * @param int|null $tagId      Tag ID filter
     *
     * @return void
     */
    public function __construct(
        public readonly ?int $categoryId = null,
        public readonly ?int $tagId = null,
    ) {
    }
}
