<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SearchController extends SearchControllerCore
{
    
    /*
  
    public function initContent()
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/search', array('entity' => 'search'));
    }


    public function doProductSearch($template, $params = array(), $locale = null)
    {
        if ($this->ajax) {
            ob_end_clean();
            header('Content-Type: application/json');
            $this->ajaxRender(json_encode($this->getAjaxProductSearchVariables()));

            return;
        } else {
            $variables = $this->getProductSearchVariables();
            $this->context->smarty->assign(array(
                'listing' => $variables,
            ));
            $this->setTemplate($template, $params, $locale);
        }
    }*/

    public function getAjaxProductSearchVariables()
    {
        $search = $this->getProductSearchVariables();
        /*
        $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
        $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
        $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));
        */
        $data = array_merge(
            array(
                'rendered_products_top' => null,
                'rendered_products' => null,
                'rendered_products_bottom' => null,
            ),
            $search
        );
        
        

        if (!empty($data['products']) && is_array($data['products'])) {
            $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
        }

        return $data;
    }

   
}
