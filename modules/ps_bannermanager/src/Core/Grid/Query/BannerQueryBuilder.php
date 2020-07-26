<?php

namespace Bannermanager\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class BannerQueryBuilder extends AbstractDoctrineQueryBuilder
{


	/*
    public function __construct(Connection $connection, $dbPrefix, $contextLangId, $contextShopId)
    {
        parent::__construct($connection, $dbPrefix);

        
    }*/


    // Get Search query builder returns QueryBuilder that is used to fetch filtered, sorted and paginated data from database.
    // This query builder is also used to get SQL query that was executed.
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('b.id_banner, b.title','b.image_name','b.is_single','b.id_parent')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());
    
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('id_banner' === $filterName) {
                $qb->andWhere("b.id_banner = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%'.$filterValue.'%');
        }

        return $qb;
    }
    
    // Get Count query builder that is used to get total count of all records (products)
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(b.id_banner)');

        return $qb;
    }
    
    // Base query can be used for both Search and Count query builders
    private function getBaseQuery()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'arkylus_banners', 'b')            
        ;
    }


}