<?php


namespace Bannermanager\Core\Grid\Definition\Factory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
//search
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;

final class BannerGridDefinationFactory extends AbstractGridDefinitionFactory
{

    const GRID_ID = 'banner';

	protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Banners', [], 'Bannermanager.Banners.List');
    }


    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_banner'))
                ->setName($this->trans('ID', [], 'Bannermanager.Banners.Admin'))
                ->setOptions([
                    'field' => 'id_banner',
                ])
            )
            ->add((new DataColumn('title'))
                ->setName($this->trans('Title', [], 'Bannermanager.Banners.Admin'))
                ->setOptions([
                    'field' => 'title',
                ])
            )
            ->add((new ImageColumn('image_name'))
                ->setName($this->trans('Image', [], 'Bannermanager.Banners.Admin'))
                ->setOptions([
                    'src_field' => 'image_name',
                ])
            )

            ->add((new DataColumn('is_single'))
                ->setName($this->trans('Single', [], 'Bannermanager.Banners.Admin'))
                ->setOptions([
                    'field' => 'is_single',
                ])
            )

            ->add((new DataColumn('id_parent'))
                ->setName($this->trans('Parent', [], 'Bannermanager.Banners.Admin'))
                ->setOptions([
                    'field' => 'id_parent',
                ])
            )
            
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'banner_edit',
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


    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_banner', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_banner')
            )
            ->add((new Filter('title', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search Title', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('title')
            )
            /*
            ->add((new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
            )*/
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'banner_list',
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }

    protected function getGridActions()
    {
        return (new GridActionCollection())
            /*
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
            )*/
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )/*
            ->add(
                (new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )*/;
    }


}