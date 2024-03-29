<?php

namespace Customerextend\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\Customer\DeleteCustomersBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer\DeleteCustomerRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
/**
 * Class CustomerGridDefinitionFactory defines customers grid structure.
 */
final class CustomerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'customer';

    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @var bool
     */
    private $isMultistoreFeatureEnabled;

    /**
     * @var array
     */
    private $genderChoices;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $isB2bFeatureEnabled
     * @param bool $isMultistoreFeatureEnabled
     * @param array $genderChoices
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $isB2bFeatureEnabled,
        $isMultistoreFeatureEnabled,
        array $genderChoices
    ) {
        parent::__construct($hookDispatcher);
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->isMultistoreFeatureEnabled = $isMultistoreFeatureEnabled;
        $this->genderChoices = $genderChoices;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Manage your Customers', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('customers_bulk'))
                ->setOptions([
                    'bulk_field' => 'id_customer',
                ])
            )
            ->add(
                (new DataColumn('id_customer'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_customer',
                ])
            )
            
            ->add(
                (new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add(
                (new DataColumn('mobile_no'))
                ->setName($this->trans('Mobile No', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'mobile_no',
                ])
            )
            ->add(
                (new DataColumn('email'))
                ->setName($this->trans('Email address', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'email',
                ])
            )
            ->add(
                (new BadgeColumn('total_spent'))
                ->setName($this->trans('Sales', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'total_spent',
                    'empty_value' => '--',
                ])
            )
            ->add(
                (new ToggleColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_toggle_status',
                    'route_param_name' => 'customerId',
                ])
            )
            
            ->add(
                (new DataColumn('date_add'))
                ->setName($this->trans('Registration', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
            ->add(
                (new DataColumn('connect'))
                ->setName($this->trans('Last visit', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'connect',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_customers_edit',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ])
                        )
                        ->add(
                            (new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Actions'))
                            ->setIcon('zoom_in')
                            ->setOptions([
                                'route' => 'admin_customers_view',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                                'clickable_row' => true,
                            ])
                        )
                        ->add((new DeleteCustomerRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'customer_id_field' => 'id_customer',
                                'customer_delete_route' => 'admin_customers_delete',
                            ])
                        ),
                ])
            );

        if ($this->isB2bFeatureEnabled) {
            $columns->addAfter(
                'email',
                (new DataColumn('company'))
                ->setName($this->trans('Company', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'company',
                ])
            );
        }

        if ($this->isMultistoreFeatureEnabled) {
            $columns->addBefore(
                'actions',
                (new DataColumn('shop_name'))
                ->setName($this->trans('Shop', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'shop_name',
                    'sortable' => false,
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_customer', NumberType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('id_customer')
            )
            
            ->add(
                (new Filter('name', TextType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search Name', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('mobile_no', TextType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search Mobile No', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('mobile_no')
            )
            ->add(
                (new Filter('email', TextType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search email', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
            )
            
            ->add(
                (new Filter('date_add', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('date_add')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'admin_customers_index',
                ])
                ->setAssociatedColumn('actions')
            );

        if ($this->isB2bFeatureEnabled) {
            $filters->add(
                (new Filter('company', TextType::class))
                ->setTypeOptions([
                    'attr' => [
                        'placeholder' => $this->trans('Search company', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ])
                ->setAssociatedColumn('company')
            );
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('import'))
                ->setName($this->trans('Import', [], 'Admin.Actions'))
                ->setIcon('cloud_upload')
                ->setOptions([
                    'route' => 'admin_import',
                    'route_params' => [
                        'import_type' => 'customers',
                    ],
                ])
            )
            ->add(
                (new LinkGridAction('export'))
                ->setName($this->trans('Export', [], 'Admin.Actions'))
                ->setIcon('cloud_download')
                ->setOptions([
                    'route' => 'admin_customers_export',
                ])
            )
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_customers_enable_bulk',
                ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_customers_disable_bulk',
                ])
            )
            ->add((new DeleteCustomersBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'customers_bulk_delete_route' => 'admin_customers_delete_bulk',
                ])
            );
    }
}
