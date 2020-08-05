<?php


namespace Customerextend\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Customerextend\Core\Domain\Customer\QueryResult\EditableCustomer;
use Customerextend\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Group\Provider\DefaultGroupsProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * Provides data for customer forms
 */
final class CustomerFormDataProvider implements FormDataProviderInterface
{
    
    private $queryBus;

    private $configuration;
    
    private $defaultGroupsProvider;

    
    public function __construct(
        CommandBusInterface $queryBus,
        ConfigurationInterface $configuration,
        DefaultGroupsProviderInterface $defaultGroupsProvider        
    ) {
        $this->queryBus = $queryBus;
        $this->configuration = $configuration;
        $this->defaultGroupsProvider = $defaultGroupsProvider;
        
    }

    /**
     * {@inheritdoc}
     */
    public function getData($customerId)
    {
        /** @var EditableCustomer $editableCustomer */
        $editableCustomer = $this->queryBus->handle(new GetCustomerForEditing((int) $customerId));    
        

        $data = [            
            'name' => $editableCustomer->getName(),            
            'email' => $editableCustomer->getEmail()->getValue(),
            'mobile_no'=>$editableCustomer->getMobileNo(),            
            'is_enabled' => $editableCustomer->isEnabled(),            
            'group_ids' => $editableCustomer->getGroupIds(),
            'default_group_id' => $editableCustomer->getDefaultGroupId(),
        ];

        

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $defaultGroups = $this->defaultGroupsProvider->getGroups();

        $data = [
            'is_enabled' => true,            
            'group_ids' => [
                $defaultGroups->getVisitorsGroup()->getId(),
                $defaultGroups->getGuestsGroup()->getId(),
                $defaultGroups->getCustomersGroup()->getId(),
            ],
            'default_group_id' => (int) $this->configuration->get('PS_CUSTOMER_GROUP'),
        ];

        

        return $data;
    }
}
