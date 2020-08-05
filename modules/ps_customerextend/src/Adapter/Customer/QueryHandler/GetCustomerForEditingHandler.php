<?php

namespace Customerextend\Adapter\Customer\QueryHandler;

use Customer;
use Customerextend\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use Customerextend\Core\Domain\Customer\Query\GetCustomerForEditing;
use Customerextend\Core\Domain\Customer\QueryHandler\GetCustomerForEditingHandlerInterface;

use Customerextend\Core\Domain\Customer\ValueObject\Email;

/**
 * Handles command that gets customer for editing
 *
 * @internal
 */
final class GetCustomerForEditingHandler implements GetCustomerForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerForEditing $query)
    {
        $customerId = $query->getCustomerId();
        $customer = new Customer($customerId->getValue());

        //var_dump($customer);die();

        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found', $customerId->getValue())
            );
        }

        

        return new EditableCustomer(
            $customerId,            
            $customer->name,            
            new Email($customer->email),            
            (bool) $customer->active,            
            $customer->getGroups(),
            (int) $customer->id_default_group,            
            (string) $customer->mobile_no

        );
    }
}
