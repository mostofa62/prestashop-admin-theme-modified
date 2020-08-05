<?php

namespace Customerextend\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use Customerextend\Core\Domain\Customer\Command\EditCustomerCommand;
use Customerextend\Core\Domain\Customer\CommandHandler\EditCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerDefaultGroupAccessException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use Customerextend\Core\Domain\Customer\Exception\DuplicateCustomerMobileNoException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\RequiredField;
use Customerextend\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;

/**
 * Handles commands which edits given customer with provided data.
 *
 * @internal
 */
final class EditCustomerHandler extends AbstractCustomerHandler implements EditCustomerHandlerInterface
{
    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var string Value of legacy _COOKIE_KEY_
     */
    private $legacyCookieKey;

    /**
     * @param Hashing $hashing
     * @param string $legacyCookieKey
     */
    public function __construct(Hashing $hashing, $legacyCookieKey)
    {
        $this->hashing = $hashing;
        $this->legacyCookieKey = $legacyCookieKey;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        $this->assertCustomerWasFound($customerId, $customer);

        $this->assertCustomerWithUpdatedEmailDoesNotExist($customer, $command);

        $this->assertCustomerWithUpdatedMobileDoesNotExist($customer, $command);
        $this->assertCustomerCanAccessDefaultGroup($customer, $command);

        $this->updateCustomerWithCommandData($customer, $command);

        
        $requiredFields = $customer->getFieldsRequiredDatabase();
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field['field_name'], $_POST)) {
                $_POST[$field['field_name']] = $customer->{$field['field_name']};
            }
        }

        $this->assertRequiredFieldsAreNotMissing($customer);

        if (false === $customer->validateFields(false)) {
            throw new CustomerException('Customer contains invalid field values');
        }

        if (false === $customer->update()) {
            throw new CustomerException('Failed to update customer');
        }
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function updateCustomerWithCommandData(Customer $customer, EditCustomerCommand $command)
    {
        

        if (null !== $command->getName()) {
            $customer->name = $command->getName();
        }

        

        if (null !== $command->getEmail()) {
            $customer->email = $command->getEmail()->getValue();
        }

        if (null !== $command->getMobileNo()) {
            $customer->mobile_no = $command->getMobileNo();
        }

        if (null !== $command->getPassword()) {
            $hashedPassword = $this->hashing->hash(
                $command->getPassword()->getValue(),
                $this->legacyCookieKey
            );

            $customer->passwd = $hashedPassword;
        }

        

        if (null !== $command->isEnabled()) {
            $customer->active = $command->isEnabled();
        }

        

        if (null !== $command->getGroupIds()) {
            $customer->groupBox = $command->getGroupIds();
        }

        if (null !== $command->getDefaultGroupId()) {
            $customer->id_default_group = $command->getDefaultGroupId();
        }

       

        
    }

    

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function assertCustomerWithUpdatedEmailDoesNotExist(Customer $customer, EditCustomerCommand $command)
    {
        // if email is not being updated
        // then assertion is not needed
        if (null === $command->getEmail()) {
            return;
        }

        if ( $command->getEmail() == (new Email($customer->email)) ) {
            return;
        }

        $customerByEmail = new Customer();
        $customerByEmail->getByEmail($command->getEmail()->getValue());

        if ($customerByEmail->id) {
            throw new DuplicateCustomerEmailException(
                $command->getEmail(),
                sprintf('Customer with email "%s" already exists', $command->getEmail()->getValue())
            );
        }
    }

    private function assertCustomerWithUpdatedMobileDoesNotExist(Customer $customer, EditCustomerCommand $command)
    {
        // if email is not being updated
        // then assertion is not needed
        if (null === $command->getMobileNo()) {
            return;
        }

        if ($command->getMobileNo() == $customer->mobile_no) {
            return;
        }

        $customerByMobile = new Customer();
        $customerByMobile->getByMobileNo($command->getMobileNo());

        if ($customerByMobile->id) {
            throw new DuplicateCustomerMobileNoException(
            $command->getMobileNo(),                
                sprintf('Customer with mobile no "%s" already exists', $command->getMobileNo())
            );
        }
    }

    /**
     * @param Customer $customer
     * @param EditCustomerCommand $command
     */
    private function assertCustomerCanAccessDefaultGroup(Customer $customer, EditCustomerCommand $command)
    {
        // if neither default group
        // nor group ids are being edited
        // then no need to assert
        if (null === $command->getDefaultGroupId()
            || null === $command->getGroupIds()
        ) {
            return;
        }

        $defaultGroupId = null !== $command->getDefaultGroupId() ?
            $command->getDefaultGroupId() :
            $customer->id_default_group
        ;
        $groupIds = null !== $command->getGroupIds() ?
            $command->getGroupIds() :
            $customer->getGroups()
         ;

        if (!in_array($defaultGroupId, $groupIds)) {
            throw new CustomerDefaultGroupAccessException(
                sprintf('Customer default group with id "%s" must be in access groups', $command->getDefaultGroupId())
            );
        }
    }
}
