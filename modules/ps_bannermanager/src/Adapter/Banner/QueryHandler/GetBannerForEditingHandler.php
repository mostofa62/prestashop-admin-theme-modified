<?php

namespace Bannermanager\Adapter\Banner\QueryHandler;

use Bannermanager\Model\Classes\Banner;
use Bannermanager\Adapter\Banner\AbstractBannerHandler;
use Bannermanager\Core\Domain\Banner\QueryHandler\GetBannerForEditingHandlerInterface;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
use Bannermanager\Core\Domain\Banner\Query\GetBannerForEditing;
use Bannermanager\Core\Domain\Banner\QueryResult\EditableBanner;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

if (!defined('_PS_BANNER_IMG_DIR_')) {
    define('_PS_BANNER_IMG_DIR_', _PS_IMG_DIR_.'bannermanager/');
}

final class GetBannerForEditingHandler extends AbstractBannerHandler implements GetBannerForEditingHandlerInterface
{

    private $imageTagSourceParser;

    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser        
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;        
    }


	public function handle(GetBannerForEditing $query)
    {
        $bannerId = $query->getBannerId();
        $banner = $this->getBanner($bannerId);

        return new EditableBanner(
            $bannerId,
            $banner->title,
            $banner->description,
            $banner->image_name,
            $this->getLogoImage($banner->image_name),            
            $banner->is_active,
            $banner->is_show_title,
            $banner->is_single,
            $banner->id_parent,
            $banner->link_type,
            $banner->link_id            
            
            //$banner->getAssociatedShops()
        );
    }

    private function getLogoImage($image_name)
    {
        
        if(!isset($image_name)) return null;

        $pathToImage = _PS_BANNER_IMG_DIR_ . $image_name;

        
        if(!file_exists($pathToImage)){

            return null;
        }

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            'banner_' . $image_name,
            150,
            null,
            true,
            true
        );

        $imageSize = file_exists($pathToImage) ? filesize($pathToImage) / 1000 : '';

        if (empty($imageTag) || empty($imageSize)) {

            return null;
        }

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }

}