<?php


namespace Customerextend\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Customerextend\Core\Domain\Customer\Command\AddCustomerCommand;
use Customerextend\Core\Domain\Customer\Command\EditCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use Customerextend\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

/**
 * Saves or updates customer data submitted in form
 */
final class CustomerFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var int
     */
    private $contextShopId;

    

    
    public function __construct(
        CommandBusInterface $bus,
        $contextShopId        
    ) {
        $this->bus = $bus;
        $this->contextShopId = $contextShopId;       
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        
        
        $command = $this->buildCustomerAddCommandFromFormData($data);

        /** @var CustomerId $customerId */
        $customerId = $this->bus->handle($command);

        //var_dump($customerId);die();

        return $customerId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($customerId, array $data)
    {
        $command = $this->buildCustomerEditCommand($customerId, $data);

        $this->bus->handle($command);
    }

    /**
     * @param array $data
     *
     * @return AddCustomerCommand
     */
    private function buildCustomerAddCommandFromFormData(array $data)
    {
        $groupIds = array_map(function ($groupId) {
            return (int) $groupId;
        }, $data['group_ids']);

        //var_dump($data);die();

        $command = new AddCustomerCommand(
            $data['name'],            
            $data['email'],            
            $data['password'],
            (int) $data['default_group_id'],
            $groupIds,
            $this->contextShopId,        
            (bool) $data['is_enabled'],            
            $data['mobile_no']
        );

        
        //var_dump($command);die();
        return $command;
    }

    /**
     * @param int $customerId
     * @param array $data
     *
     * @return EditCustomerCommand
     */
    private function buildCustomerEditCommand($customerId, array $data)
    {
        $groupIds = array_map(function ($groupId) {
            return (int) $groupId;
        }, $data['group_ids']);

        $command = (new EditCustomerCommand($customerId))            
            ->setEmail($data['email'])            
            ->setName($data['name'])           
            ->setIsEnabled($data['is_enabled'])            
            ->setDefaultGroupId((int) $data['default_group_id'])
            ->setGroupIds($groupIds)            
            ->setMobileNo($data['mobile_no'])
        ;

        if (null !== $data['password']) {
            $command->setPassword($data['password']);
        }

        

        return $command;
    }
}
