<?php

namespace Customerextend\Core\Domain\Customer\Query;

use Customerextend\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Gets customer information for editing.
 */
class GetCustomerForEditing
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
