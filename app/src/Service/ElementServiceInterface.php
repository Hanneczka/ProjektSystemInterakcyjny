<?php

namespace App\Service;

use App\Entity\Element;

interface ElementServiceInterface {
public function save(Element $element): void;
}
