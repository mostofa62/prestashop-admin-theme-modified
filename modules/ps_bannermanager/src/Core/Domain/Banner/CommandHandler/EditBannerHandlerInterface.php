<?php


namespace Bannermanager\Core\Domain\Banner\CommandHandler;

use Bannermanager\Core\Domain\Banner\Command\EditBannerCommand;


interface EditBannerHandlerInterface
{
    /**
     * @param AddBannerCommand $command
     *
     * @return BannerId
     */
    public function handle(EditBannerCommand $command);
}