<?php


namespace Customerextend\Core\Domain\Customer\QueryHandler;

use Customerextend\Core\Domain\Customer\QueryResult\EditableCustomer;
use Customerextend\Core\Domain\Customer\Query\GetCustomerForEditing;

/**
 * Interface for service that gets customer data for editing
 */
interface GetCustomerForEditingHandlerInterface
{
    /**
     * @param GetCustomerForEditing $query
     *
     * @return EditableCustomer
     */
    public function handle(GetCustomerForEditing $query);
}
