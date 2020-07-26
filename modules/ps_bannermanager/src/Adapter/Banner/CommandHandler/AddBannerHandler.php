<?php

namespace Bannermanager\Adapter\Banner\CommandHandler;

use Bannermanager\Model\Classes\Banner;
use Bannermanager\Adapter\Banner\AbstractBannerHandler;
use Bannermanager\Core\Domain\Banner\Command\AddBannerCommand;
use Bannermanager\Core\Domain\Banner\CommandHandler\AddBannerHandlerInterface;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;


final class AddBannerHandler extends AbstractBannerHandler implements AddBannerHandlerInterface
{
	
	public function handle(AddBannerCommand $command)
    {
        $banner = new Banner();
        $this->fillLegacyBannerWithData($banner, $command);

        try {
            if (false === $banner->validateFields(false)) {
                throw new BannerException('Banner contains invalid field values');
            }

            if (!$banner->add()) {
                throw new BannerException(
                    sprintf('Failed to add new banner "%s"', $command->getTitle())
                );
            }           
        } catch (\PrestaShopException $e) {
            file_put_contents(__DIR__."/ex.txt", $e);
            throw new BannerException(
                sprintf('Failed to add new banner "%s"', $command->getTitle())
            );
        }

        return new BannerId((int) $banner->id);
    }


    private function fillLegacyBannerWithData(Banner $banner, AddBannerCommand $command)
    {
        $banner->title = $command->getTitle();
        $banner->description = $command->getDescription();
        $banner->is_active = $command->getActive();
        $banner->image_name = $command->getImageName();
        $banner->is_show_title = $command->getShowTitle();
        $banner->is_single = $command->getSingle(); 
        $banner->id_parent = $command->getParent();

        $banner->link_type = $command->getLinkType(); 
        $banner->link_id = $command->getLinkId();       
    }
}