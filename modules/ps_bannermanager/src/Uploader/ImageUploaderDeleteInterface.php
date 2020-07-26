<?php

namespace Bannermanager\Uploader;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;

interface ImageUploaderDeleteInterface extends ImageUploaderInterface{


	public function deleteOldImage($bannerImage);
}