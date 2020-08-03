<?php

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
    }

    public function getAjaxProductSearchVariables()
    {
        $search = $this->getProductSearchVariables();
        
        $rendered_products_top = $this->render('catalog/_partials/products-top', array('listing' => $search));
        $rendered_products = $this->render('catalog/_partials/products', array('listing' => $search));
        $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', array('listing' => $search));
        
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
    }*/

   
}
