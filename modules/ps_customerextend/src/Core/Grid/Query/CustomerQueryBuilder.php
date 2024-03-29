<?php

namespace Customerextend\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
/**
 * Class CustomerQueryBuilder builds queries to fetch data for customers grid.
 */
final class CustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('c.id_customer, c.name, c.mobile_no, c.email, c.active')
            ->addSelect('c.date_add, s.name as shop_name, c.company');

        $this->appendTotalSpentQuery($searchQueryBuilder);
        $this->appendLastVisitQuery($searchQueryBuilder);
        $this->applySorting($searchQueryBuilder, $searchCriteria);

        $this->criteriaApplicator->applyPagination(
            $searchCriteria,
            $searchQueryBuilder
        );

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getCustomerQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'customer', 'c')
            
            ->leftJoin(
                'c',
                $this->dbPrefix . 'shop',
                's',
                'c.id_shop = s.id_shop'
            )
            ->where('c.deleted = 0')
            ->andWhere('c.id_shop IN (:context_shop_ids)')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendTotalSpentQuery(QueryBuilder $queryBuilder)
    {
        $totalSpentQueryBuilder = $this->connection->createQueryBuilder()
            ->select('SUM(total_paid_real / conversion_rate)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.id_customer = c.id_customer')
            ->andWhere('o.id_shop IN (:context_shop_ids)')
            ->andWhere('o.valid = 1')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $queryBuilder->addSelect('(' . $totalSpentQueryBuilder->getSQL() . ') as total_spent');
    }

    /**
     * Append "last visit" column to customers query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendLastVisitQuery(QueryBuilder $queryBuilder)
    {
        $lastVisitQueryBuilder = $this->connection->createQueryBuilder()
            ->select('c.date_add')
            ->from($this->dbPrefix . 'guest', 'g')
            ->leftJoin('g', $this->dbPrefix . 'connections', 'con', 'con.id_guest = g.id_guest')
            ->where('g.id_customer = c.id_customer')
            ->orderBy('c.date_add', 'DESC')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $lastVisitQueryBuilder->getSQL() . ') as connect');
    }

    /**
     * Apply filters to customers query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_customer',
            //'social_title',
            'name',
            'mobile_no',
            'email',
            'active',
            //'newsletter',
            //'optin',
            'date_add',
            //'company',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['active', 'id_customer'])) {
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('c.`' . $filterName . '` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }
            

            if ('date_add' === $filterName) {
                $qb->andWhere('c.date_add >= :date_from AND c.date_add <= :date_to');
                $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));

                if (isset($filterValue['from'])) {
                    $qb->andWhere('c.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('c.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $qb->andWhere('`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * Apply sorting so search query builder for customers.
     *
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria)
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_customer':
            case 'name':
            case 'mobile_no':
            case 'email':
            case 'date_add':
            case 'company':
            case 'active':
            //case 'newsletter':
            //case 'optin':
                $orderBy = 'c.' . $searchCriteria->getOrderBy();

                break;
            /*case 'social_title':
                $orderBy = 'gl.name';

                break;
            case 'connect':*/
            case 'total_spent':
                $orderBy = $searchCriteria->getOrderBy();

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
