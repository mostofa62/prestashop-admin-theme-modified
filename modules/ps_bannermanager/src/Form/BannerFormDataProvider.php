<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Bannermanager\Form;


use Bannermanager\Model\Banner;
use Bannermanager\Repository\BannermanagerRepository;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
//use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface


/**
 * Class LinkBannerFormDataProvider.
 */
class BannerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var int|null
     */
    private $idBanner;

    /**
     * @var BannermanagerRepository
     */
    private $repository;

    

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var array
     */
    private $languages;

    /**
     * @var int
     */
    private $shopId;

    /**
     * LinkBlockFormDataProvider constructor.
     *
     * @param LinkBlockRepository $repository
     * @param LinkBlockCacheInterface $cache
     * @param ModuleRepository $moduleRepository
     * @param array $languages
     * @param int $shopId
     */
    public function __construct(
        BannermanagerRepository $repository,
        
        ModuleRepository $moduleRepository,
        array $languages,
        $shopId
    ) {
        $this->repository = $repository;        
        $this->moduleRepository = $moduleRepository;
        $this->languages = $languages;
        $this->shopId = $shopId;
    }

    /**
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    
    public function getData()
    {
        if (null === $this->idBanner) {
            return [];
        }

        $Banner = new Banner($this->idBanner);

        $arrayBanner = $Banner->toArray();

        

        return ['banner' => [
            'id_banner' => $arrayBanner['id_banner'],
            'title' => $arrayBanner['title'],
            'description' => $arrayBanner['description'],
            'image_name'=>$arrayBanner['image_name']                
        ]];
    }

    
    

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShop\PrestaShop\Adapter\Entity\PrestaShopDatabaseException
     */
    public function setData(array $data)
    {
        $Banner = $data['banner'];
        
        $errors = $this->validateBanner($Banner);
        if (!empty($errors)) {
            return $errors;
        }
       

        if (empty($Banner['id_banner'])) {
            $BannerId = $this->repository->create($Banner);
            $this->setIdBanner($BannerId);
        } else {
            $BannerId = $Banner['id_banner'];
            $this->repository->update($BannerId, $Banner);
        }
        //$this->updateHook($linkBlock['id_hook']);
        //$this->cache->clearModuleCache();

        return [];
    }

    /**
     * @return int
     */
    public function getIdBanner()
    {
        return $this->idBanner;
    }

    /**
     * @param int $idLinkBlock
     *
     * @return LinkBlockFormDataProvider
     */
    public function setIdBanner($idBanner)
    {
        $this->idBanner = $idBanner;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */

    
    private function validateBanner(array $data)
    {
        $errors = [];
       

        if (!isset($data['title'])) {
            $errors[] = [
                'key' => 'Missing Title',
                'domain' => 'Admin.Catalog.Notification',
                'parameters' => [],
            ];
        } 
       
        

        return $errors;
    }

    /**
     * @param array $custom
     *
     * @return bool
     */
    private function isEmptyCustom(array $custom)
    {
        $fields = ['title', 'url'];
        foreach ($custom as $langCustom) {
            foreach ($fields as $field) {
                if (!empty($langCustom[$field])) {
                    return false;
                }
            }
        }

        return true;
    }

   
}
