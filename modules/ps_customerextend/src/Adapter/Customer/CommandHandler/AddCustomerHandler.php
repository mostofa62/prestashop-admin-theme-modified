<?php


namespace Customerextend\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use Customerextend\Core\Domain\Customer\Command\AddCustomerCommand;
use Customerextend\Core\Domain\Customer\CommandHandler\AddCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerDefaultGroupAccessException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\DuplicateCustomerEmailException;
use Customerextend\Core\Domain\Customer\Exception\DuplicateCustomerMobileNoException;
use Customerextend\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\RequiredField;
use Customerextend\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Adapter\Customer\CommandHandler\AbstractCustomerHandler;
/**
 * Handles command that adds new customer
 *
 * @internal
 */
final class AddCustomerHandler extends AbstractCustomerHandler implements AddCustomerHandlerInterface
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
    public function handle(AddCustomerCommand $command)
    {
        $customer = new Customer();

        

        $this->fillCustomerWithCommandData($customer, $command);


        

        $this->assertRequiredFieldsAreNotMissing($customer);
        

        if (false === $customer->validateFields(false)) {
            //var_dump($customer->validateFields(false));die();
            throw new CustomerException('Customer contains invalid field values');
        }



        $this->assertCustomerWithGivenEmailDoesNotExist($command->getEmail());

        $this->assertCustomerWithGivenMobileDoesNotExist($command->getMobileNo());
        $this->assertCustomerCanAccessDefaultGroup($command);


        $customer->add();

        return new CustomerId((int) $customer->id);
    }

    /**
     * @param Email $email
     */
    private function assertCustomerWithGivenEmailDoesNotExist(Email $email)
    {
        $customer = new Customer();
        if($email->getValue()!==null){
            $customer->getByEmail($email->getValue());
        }

        if ($customer->id) {
            throw new DuplicateCustomerEmailException(
                $email,
                sprintf('Customer with email "%s" already exists', $email->getValue())
            );
        }
    }

    private function assertCustomerWithGivenMobileDoesNotExist($mobileNo)
    {
        $customer = new Customer();

        $customer->getByMobileNo($mobileNo);
        //var_dump($customer->id);die();


        if ($customer->id) {
            throw new DuplicateCustomerMobileNoException(
                $mobileNo,                
                sprintf('Customer with mobile no "%s" already exists', $mobileNo),0
            );
        }
    }

    /**
     * @param Customer $customer
     * @param AddCustomerCommand $command
     */
    private function fillCustomerWithCommandData(Customer $customer, AddCustomerCommand $command)
    {
        

        $hashedPassword = $this->hashing->hash(
            $command->getPassword()->getValue(),
            $this->legacyCookieKey
        );

        $customer->name = $command->getName();       
        $customer->email = $command->getEmail()->getValue();
        $customer->mobile_no = $command->getMobileNo();
        $customer->passwd = $hashedPassword;
        $customer->id_default_group = $command->getDefaultGroupId();
        $customer->groupBox = $command->getGroupIds();       
        $customer->active = $command->isEnabled();        
        $customer->id_shop = $command->getShopId();

        
    }

    /**
     * @param AddCustomerCommand $command
     */
    private function assertCustomerCanAccessDefaultGroup(AddCustomerCommand $command)
    {
        if (!in_array($command->getDefaultGroupId(), $command->getGroupIds())) {
            throw new CustomerDefaultGroupAccessException(
                sprintf('Customer default group with id "%s" must be in access groups', $command->getDefaultGroupId())
            );
        }
    }

    
}
