<?php


namespace Customerextend\Core\Domain\Customer\QueryHandler;

use Customerextend\Core\Domain\Customer\QueryResult\ViewableCustomer;
use Customerextend\Core\Domain\Customer\Query\GetCustomerForViewing;

/**
 * Class GetCustomerInformationHandlerInterface.
 */
interface GetCustomerForViewingHandlerInterface
{
    /**
     * @param GetCustomerForViewing $query
     *
     * @return ViewableCustomer
     */
    public function handle(GetCustomerForViewing $query);
}
