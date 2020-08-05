<?php

namespace Customerextend\Core\Domain\Customer\QueryHandler;

use Customerextend\Core\Domain\Customer\Query\SearchCustomers;

/**
 * Interface for service that handlers customers searching command
 */
interface SearchCustomersHandlerInterface
{
    /**
     * @param SearchCustomers $query
     *
     * @return array
     */
    public function handle(SearchCustomers $query);
}
