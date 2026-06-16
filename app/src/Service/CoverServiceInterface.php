<?php
/**
 * Cover service interface.
 */

namespace App\Service;

use App\Entity\Cover;
use App\Entity\Element;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Cover service.
 */
interface CoverServiceInterface
{
    /**
     * Create cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover       $cover       Cover entity
     * @param Element         $element         Element entity
     */
    public function create(UploadedFile $uploadedFile, Cover $cover, Element $element): void;
    public function update(UploadedFile $uploadedFile, Cover $cover): void;
    public function delete(Cover $cover): void;
}

