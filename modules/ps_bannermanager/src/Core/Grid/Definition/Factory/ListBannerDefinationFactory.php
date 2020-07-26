<?php


namespace Bannermanager\Core\Grid\Definition\Factory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;

final class ListBannerDefinationFactory extends AbstractGridDefinitionFactory
{
	
	protected function getId()
    {
        return 'banner_manager';
    }

    protected function getName()
    {
        return $this->trans('Banner Manager', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_banner'))
                ->setName($this->trans('ID', [], 'Modules.Linklist.Admin'))
                ->setOptions([
                    'field' => 'id_banner',
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('Name of the block', [], 'Modules.Linklist.Admin'))
                ->setOptions([
                    'field' => 'title',
                ])
            )
            
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_banner_edit',
                                'route_param_name' => 'bannerId',
                                'route_param_field' => 'id_banner',
                            ])
                        )/*
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'POST',
                                'route' => 'admin_banner_delete',
                                'route_param_name' => 'bannerId',
                                'route_param_field' => 'id_banner',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),*/
                ])
            )
        ;
    
    }
}