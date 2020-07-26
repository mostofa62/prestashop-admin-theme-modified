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

namespace Bannermanager\Form\IdentifiableObject\DataProvider;


use Bannermanager\Model\Banner;
use Bannermanager\Repository\BannermanagerRepository;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class BannermanagerFormDataProvider implements FormDataProviderInterface
{
    
   
    
    public function getData($bannerId)
    {
        $bannerObjectModel = new Banner($bannerId);
        
        // check that the element exists in db
        if (empty($bannerObjectModel->id_banner)) {
            throw new PrestaShopObjectNotFoundException('Object not found');
        }

        return [
            'id_banner'=>$bannerObjectModel->id_banner,
            'title' => $bannerObjectModel->title,            
            'description' => $bannerObjectModel->description,
            'image_name'=>$bannerObjectModel->image_name,
            'is_active'=>$bannerObjectModel->is_active,
        ];
    }

    public function getDefaultData()
    {
        return [
            'id_banner'=>null,
            'title' => null,            
            'description' => null,
            'image_name'=>null,
            'is_active'=>0
        ];
    }
    

   

   
}
