<?php


namespace Bannermanager\Core\Domain\Banner\CommandHandler;

use Bannermanager\Core\Domain\Banner\Command\AddBannerCommand;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;

/**
 * Defines contract for AddManufacturerHandler
 */
interface AddBannerHandlerInterface
{
    /**
     * @param AddBannerCommand $command
     *
     * @return BannerId
     */
    public function handle(AddBannerCommand $command);
}
