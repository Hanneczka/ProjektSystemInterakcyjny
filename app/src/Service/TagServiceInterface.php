<?php

namespace App\Service;

use App\Entity\Tag;

interface TagServiceInterface {
public function save(Tag $tag): void;
    public function delete(Tag $tag): void;

}
