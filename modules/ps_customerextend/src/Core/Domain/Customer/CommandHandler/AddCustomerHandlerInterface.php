<?php


namespace Customerextend\Core\Domain\Customer\CommandHandler;

use Customerextend\Core\Domain\Customer\Command\AddCustomerCommand;
use Customerextend\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Interface for service that handles command that adds new customer
 */
interface AddCustomerHandlerInterface
{
    /**
     * @param AddCustomerCommand $command
     *
     * @return CustomerId
     */
    public function handle(AddCustomerCommand $command);
}
