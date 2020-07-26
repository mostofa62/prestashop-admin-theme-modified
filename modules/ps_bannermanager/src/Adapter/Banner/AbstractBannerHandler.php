<?php

namespace Bannermanager\Adapter\Banner;

use Bannermanager\Model\Classes\Banner;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
use PrestaShopException;


abstract class AbstractBannerHandler extends AbstractObjectModelHandler
{
	
	protected function getBanner(BannerId $bannerId)
    {
        try {
            $banner = new Banner($bannerId->getValue());
        } catch (PrestaShopException $e) {
            throw new BannerException('Failed to create new banner', 0, $e);
        }

        if ($banner->id !== $bannerId->getValue()) {
            throw new BannerNotFoundException(
                sprintf('Banner with id "%s" was not found.', $bannerId->getValue())
            );
        }

        return $banner;
    }
}