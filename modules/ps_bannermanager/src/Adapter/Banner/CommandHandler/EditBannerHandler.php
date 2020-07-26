<?php

namespace Bannermanager\Adapter\Banner\CommandHandler;

use Bannermanager\Model\Classes\Banner;
use Bannermanager\Adapter\Banner\AbstractBannerHandler;
use Bannermanager\Core\Domain\Banner\Command\EditBannerCommand;
use Bannermanager\Core\Domain\Banner\CommandHandler\EditBannerHandlerInterface;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
use PrestaShopException;

final class EditBannerHandler extends AbstractBannerHandler implements EditBannerHandlerInterface{


	public function handle(EditBannerCommand $command)
    {
        $bannerId = $command->getBannerId();
        $banner = $this->getBanner($bannerId);
        $this->populateBannerWithData($banner, $command);

        try {
            if (false === $banner->validateFields(false)) {
                throw new BannerException('Banner contains invalid field values');
            }

            

            if (!$banner->update()) {
                throw new BannerException(
                    sprintf('Cannot update banner with id "%s"', $banner->id)
                );
            }
        } catch (PrestaShopException $e) {
            throw new BannerException(
                sprintf('Cannot update banner with id "%s"', $banner->id)
            );
        }
    }

    private function populateBannerWithData(Banner $banner, EditBannerCommand $command)
    {
        if (null !== $command->getTitle()) {
            $banner->title = $command->getTitle();
        }

        if (null !== $command->getDescription()) {
            $banner->description = $command->getDescription();
        }
        
        if (null !== $command->getActive()) {
            $banner->is_active = $command->getActive();
        }

        if (null !== $command->getImageName()) {
            $banner->image_name = $command->getImageName();
        }

        if (null !== $command->getShowTitle()) {
            $banner->is_show_title = $command->getShowTitle();
        }
        if (null !== $command->getSingle()) {
            $banner->is_single = $command->getSingle();
        }

        if (null !== $command->getParent()) {
            $banner->id_parent = $command->getParent();
        }

        if (null !== $command->getLinkType()) {
            $banner->link_type = $command->getLinkType();
        }

        if (null !== $command->getLinkId()) {
            $banner->link_id = $command->getLinkId();
        }
        
    }


}