<?php


namespace Bannermanager\Core\Search\Filters;


use Bannermanager\Core\Grid\Definition\Factory\BannerGridDefinationFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;


final class BannerFilters extends Filters
{
    /** @var string */
    protected $filterId = BannerGridDefinationFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'title',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}