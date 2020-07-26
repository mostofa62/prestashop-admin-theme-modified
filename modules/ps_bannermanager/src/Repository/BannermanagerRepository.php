<?php


namespace Bannermanager\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
//use Symfony\Component\Translation\TranslatorInterface;
use PDO;

class BannermanagerRepository {


	/**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var array
     */
    //private $languages;

    /**
     * @var TranslatorInterface
     */
    //private $translator;

    /**
     * LinkBlockRepository constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param array $languages
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix/*,
        array $languages,
        //TranslatorInterface $translator*/
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        //$this->languages = $languages;
        //$this->translator = $translator;
    }

    public function getBannerList()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('h.id_banner, h.title')
            ->from($this->dbPrefix . 'arkylus_bannermanager', 'h')            
            ->orderBy('h.title')
        ;

        return $qb->execute()->fetchAll();
    }


    public function create(array $data)
    {
        
        

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->dbPrefix . 'arkylus_bannermanager')
            ->values([
                'title' => ':title',
                'description' => ':description',
                'image_name' =>':image_name',
                'is_active' =>':is_active'                
            ])
            ->setParameters([
                'title' => empty($data['title']) ? null : $data['title'],
                'description' => empty($data['description']) ? null : $data['description'],
                'image_name' => empty($data['image_name']) ? null : $data['image_name'],
                'is_active'=>$data['is_active']                 
            ]);

        $this->executeQueryBuilder($qb, 'Banner error');
        $BannerId = $this->connection->lastInsertId();       

        return $BannerId;
    }


    public function update($bannerId, array $data)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->dbPrefix . 'arkylus_bannermanager', 'lb')
            ->andWhere('lb.id_banner = :bannerId')
            ->set('title', ':title')
            ->set('description', ':description')
            ->set('image_name', ':image_name')
            ->set('is_active', ':is_active')
            ->setParameters([
                'bannerId' => $bannerId,
                'title' => $data['title'],
                'description' => $data['description'],
                'image_name' => $data['image_name'],
                'is_active'=>$data['is_active'] 
                
            ])
        ;
        $this->executeQueryBuilder($qb, 'Banner error');

        
    }

    public function findImageByBanner($bannerId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('`image_name`')
            ->from($this->dbPrefix . 'arkylus_bannermanager')
            ->where('`id_banner` = :id_banner')
        ;

        $queryBuilder->setParameter('id_banner', $bannerId);

        return $queryBuilder->execute()->fetch(PDO::FETCH_COLUMN);
    }


    private function executeQueryBuilder(QueryBuilder $qb, $errorPrefix = 'SQL error')
    {
        $statement = $qb->execute();
        if ($statement instanceof Statement && !empty($statement->errorInfo())) {
            throw new DatabaseException($errorPrefix . ': ' . var_export($statement->errorInfo(), true));
        }

        return $statement;
    }





}
