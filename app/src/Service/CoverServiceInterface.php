<?php

/**
 * Cover service interface.
 */

namespace App\Service;

use App\Entity\Cover;
use App\Entity\Element;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface CoverServiceInterface.
 */
interface CoverServiceInterface
{
    /**
     * Create cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     * @param Element      $element      Element entity
     */
    public function create(UploadedFile $uploadedFile, Cover $cover, Element $element): void;

    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     */
    public function update(UploadedFile $uploadedFile, Cover $cover): void;

    /**
     * Delete cover.
     *
     * @param Cover $cover Cover entity
     */
    public function delete(Cover $cover): void;
}
