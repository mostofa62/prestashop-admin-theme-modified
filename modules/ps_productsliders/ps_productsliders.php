<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use Productsliders\Models\Category;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Productsliders\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;


class Ps_ProductSliders extends Module implements WidgetInterface
{

	
	public $templateFile;

	public function __construct()
    {
        $this->name = 'ps_productsliders';
        $this->author = 'Golam Mostofa';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = false;
        parent::__construct();

        $this->displayName = $this->trans('Arkylus Featuredproducts Sliders', array(), 'Modules.Featuredproducts.Admin');
        $this->description = $this->trans('Displays featured products in the central column of your homepage.', array(), 'Modules.ProductSliders.Admin');

        $this->templateFile = 'module:ps_productsliders/views/templates/hook/ps_productsliders.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue('HOME_FEATURED_NBR', 8);
        Configuration::updateValue('HOME_FEATURED_CAT', (int) Context::getContext()->shop->getCategory());
        Configuration::updateValue('HOME_FEATURED_RANDOMIZE', false);


        return parent::install();
        //&& $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $categoryId = isset($configuration['category'])?(int)$configuration['category']:(int) Configuration::get('HOME_FEATURED_CAT');
        
        

        if (!$this->isCached($this->templateFile, $this->getCacheId('ps_productsliders'.$categoryId))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('ps_productsliders'.$categoryId));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'HOME_FEATURED_NBR' => Tools::getValue('HOME_FEATURED_NBR', (int) Configuration::get('HOME_FEATURED_NBR')),
            'HOME_FEATURED_CAT' => Tools::getValue('HOME_FEATURED_CAT', (int) Configuration::get('HOME_FEATURED_CAT')),
            'HOME_FEATURED_RANDOMIZE' => Tools::getValue('HOME_FEATURED_RANDOMIZE', (bool) Configuration::get('HOME_FEATURED_RANDOMIZE')),
        );
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        
        $category = isset($configuration['category'])?(int)$configuration['category']:(int) Configuration::get('HOME_FEATURED_CAT'); 
        
        $sort = isset($configuration['sort'])?$configuration['sort']:'asc';

        $products = $this->getProducts($category, $sort);

        if (!empty($products['products'])) {
            
            
            return array(
                'products' => $products['products'],
                'category' => $products['category'],
                'allProductsLink' => Context::getContext()->link->getCategoryLink($category),
            );
        }
        return false;
    }

    protected function getProducts($categoryId = null, $sort='asc')
    {
            
        

        $category = new Category($categoryId);
        
        

        $searchProvider = new CategoryProductSearchProvider(
            $this->context->getTranslator(),
            $category
        );
        
        

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = Configuration::get('HOME_FEATURED_NBR');
        if ($nProducts < 0) {
            $nProducts = 12;
        }

        

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        /*if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
            $query->setSortOrder(SortOrder::random());
        } else {
            $query->setSortOrder(new SortOrder('product', 'position', 'asc'));
        }*/
        
        $query->setSortOrder(new SortOrder('product', 'price', $sort));

        $result = $searchProvider->runQuery(
            $context,
            $query
        );
        
        //var_dump($result);die();

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return  [
            'products'=>$products_for_template, 
            'category'=>$category
        ];
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }
    
    public function hookActionFrontControllerSetMedia($params)
    {
        
    
        /*// On every pages
        $this->context->controller->registerJavascript(
            "{$this->name}",
            "modules/".$this->name."/{$this->name}.js",
            [
              //'position' => 'head',
              //'inline' => true,
              //'priority' => 10,
            ]
        );*/
    }

}