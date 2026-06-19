<?php

/**
 * Cover service.
 */

namespace App\Service;

use App\Entity\Cover;
use App\Repository\CoverRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Element;

/**
 * Class CoverService.
 */
class CoverService implements CoverServiceInterface
{
    /**
     * Constructor.
     *
     * @param CoverRepository            $coverRepository   Cover repository
     * @param FileUploadServiceInterface $fileUploadService File upload service
     *
     * @return void
     */
    public function __construct(private readonly CoverRepository $coverRepository, private readonly FileUploadServiceInterface $fileUploadService,)
    {
    }

    /**
     * Create cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     * @param Element      $element      Element entity
     *
     * @return void
     */
    public function create(UploadedFile $uploadedFile, Cover $cover, Element $element): void
    {
        $coverFilename = $this->fileUploadService->upload($uploadedFile);

        $cover->setElement($element);
        $cover->setFileName($coverFilename);
        $this->coverRepository->save($cover);
    }

    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     *
     * @return void
     */
    public function update(UploadedFile $uploadedFile, Cover $cover): void
    {
        $oldFilename = $cover->getFileName();
        if ($oldFilename) {
            $oldFilePath = $this->fileUploadService->getTargetDirectory() . '/' . $oldFilename;
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $newCoverFilename = $this->fileUploadService->upload($uploadedFile);

        $cover->setFileName($newCoverFilename);
        $this->coverRepository->save($cover);
    }

    /**
     * Delete cover.
     *
     * @param Cover $cover Cover entity
     *
     * @return void
     */
    public function delete(Cover $cover): void
    {
        $filename = $cover->getFileName();
        if ($filename) {
            $filePath = $this->fileUploadService->getTargetDirectory() . '/' . $filename;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->coverRepository->delete($cover);
    }
}
