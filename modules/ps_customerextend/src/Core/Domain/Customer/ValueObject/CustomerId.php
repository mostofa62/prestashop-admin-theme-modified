<?php

namespace Customerextend\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Defines Customer ID with it's constraints
 */
class CustomerId
{
    /**
     * @var int
     */
    private $customerId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->assertIntegerIsGreaterThanZero($customerId);

        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    private function assertIntegerIsGreaterThanZero($customerId)
    {
        if (!is_int($customerId) || 0 > $customerId) {
            throw new CustomerException(
                sprintf(
                    'Customer id %s is invalid. Customer id must be number that is greater than zero.',
                    var_export($customerId, true)
                )
            );
        }
    }
}
