<?php

namespace App\Repository;

class CategoriesRepository
{
    private array $data = [
        1 => [
            'id'=> 1,
            'title' => 'Books',
        ],
        2 => [
            'id'=> 2,
            'title' => 'Music',
        ],
        3 => [
            'id'=> 3,
            'title' => 'Movies',
        ],
    ];
    public function findAll(): array
    {
        return $this->data;
    }
    public function findOneById(int $id): ?array
    {
        return count($this->data) && isset($this->data[$id])
            ? $this->data[$id]
            : null;
    }
}
