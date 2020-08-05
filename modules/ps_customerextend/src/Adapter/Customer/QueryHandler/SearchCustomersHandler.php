<?php


namespace Customerextend\Adapter\Customer\QueryHandler;

use Customer;
use Customerextend\Core\Domain\Customer\Query\SearchCustomers;
use Customerextend\Core\Domain\Customer\QueryHandler\SearchCustomersHandlerInterface;

/**
 * Handles query that searches for customers by given phrases
 *
 * @internal
 */
final class SearchCustomersHandler implements SearchCustomersHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SearchCustomers $query)
    {
        $limit = 50;
        $phrases = array_unique($query->getPhrases());

        $customers = [];

        foreach ($phrases as $searchPhrase) {
            if (empty($searchPhrase)) {
                continue;
            }

            $customersResult = Customer::searchByName($searchPhrase, $limit);
            if (!is_array($customersResult)) {
                continue;
            }

            foreach ($customersResult as $customerArray) {
                if (!$customerArray['active']) {
                    continue;
                }

                $customerArray['fullname_and_email'] = sprintf(
                    '%s %s - %s',
                    $customerArray['name'],
                    $customerArray['mobile_no'],
                    $customerArray['email']
                );

                unset(
                    $customerArray['passwd'],
                    $customerArray['secure_key'],
                    $customerArray['last_passwd_gen'],
                    $customerArray['reset_password_token'],
                    $customerArray['reset_password_validity']
                );
                $customers[$customerArray['id_customer']] = $customerArray;

            }
        }

        return $customers;
    }
}
