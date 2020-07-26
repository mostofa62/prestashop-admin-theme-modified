<?php

namespace Bannermanager\Uploader;

use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Bannermanager\Repository\BannermanagerRepository;
use Bannermanager\Uploader\ImageUploaderDeleteInterface;

if (!defined('_PS_BANNER_IMG_DIR_')) {
    define('_PS_BANNER_IMG_DIR_', _PS_IMG_DIR_.'bannermanager/');
}


class BannerImageUploader implements ImageUploaderDeleteInterface
{

    


	public function upload($bannerId, UploadedFile $image)
    {
        

        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);
        
        

        $originalImageName = $image->getClientOriginalName();
        $destination = _PS_BANNER_IMG_DIR_ . $originalImageName;
        $this->uploadFromTemp($tempImageName, $destination);
        //$this->supplierExtraImageRepository->upsertSupplierImageName($supplierId, $originalImageName);
        return $originalImageName;
    }

    protected function createTemporaryImage(UploadedFile $image)
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName || !move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('Failed to create temporary image file');
        }

        return $temporaryImageName;
    }

    protected function uploadFromTemp($temporaryImageName, $destination)
    {
        if (!\ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }

        if (!\ImageManager::resize($temporaryImageName, $destination)) {
            throw new ImageOptimizationException(
                'An error occurred while uploading the image. Check your directory permissions.'
            );
        }

        unlink($temporaryImageName);
    }

    /**
     * Deletes old image
     *
     * @param $supplierId
     */
    public  function deleteOldImage($bannerImage)
    {
        /** @var SupplierExtraImage $supplierExtraImage */       

        if (isset($bannerImage) && file_exists(_PS_BANNER_IMG_DIR_ . $bannerImage))
        {
        //var_dump(_PS_BANNER_IMG_DIR_ . $bannerImage);die();

            @unlink(_PS_BANNER_IMG_DIR_ . $bannerImage);
        }
    }

    /**
     * Check if image is allowed to be uploaded.
     *
     * @param UploadedFile $image
     *
     * @throws UploadedImageConstraintException
     */
    protected function checkImageIsAllowedForUpload(UploadedFile $image)
    {
        $maxFileSize = \Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && $image->getSize() > $maxFileSize) {
            throw new UploadedImageConstraintException(
                sprintf(
                   'Max file size allowed is "%s" bytes. Uploaded image size is "%s".', 
                    $maxFileSize, $image->getSize()
                ), 
                UploadedImageConstraintException::EXCEEDED_SIZE
            );
        }

        if (!\ImageManager::isRealImage($image->getPathname(), $image->getClientMimeType())
            || !\ImageManager::isCorrectImageFileExt($image->getClientOriginalName())
            || preg_match('/\%00/', $image->getClientOriginalName()) // prevent null byte injection
        ) {
            throw new UploadedImageConstraintException(
                sprintf(
                    'Image format "%s", not recognized, allowed formats are: .gif, .jpg, .png', 
                    $image->getClientOriginalExtension()
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT
            );
        }
    }



}


