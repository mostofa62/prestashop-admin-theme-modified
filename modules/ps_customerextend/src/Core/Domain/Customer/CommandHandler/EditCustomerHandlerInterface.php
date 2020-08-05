<?php


namespace Customerextend\Core\Domain\Customer\CommandHandler;

use Customerextend\Core\Domain\Customer\Command\EditCustomerCommand;

/**
 * Interface for service that handles customer editing command
 */
interface EditCustomerHandlerInterface
{
    /**
     * @param EditCustomerCommand $command
     */
    public function handle(EditCustomerCommand $command);
}
