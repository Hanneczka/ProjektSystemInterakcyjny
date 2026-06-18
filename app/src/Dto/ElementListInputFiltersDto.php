<?php

namespace App\Dto;

class ElementListInputFiltersDto
{
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null)
    {
    }
}
