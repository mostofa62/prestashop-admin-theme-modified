<?php

namespace Productsliders\Models;
use Tools;
use Configuration;
use Validate;
use Combination;
use Shop;
use Product;
use Db;
use Context;

class Category extends \ObjectModel
{
    public $id;

    /** @var int category ID */
    public $id_category;

    /** @var mixed string or array of Name */
    public $name;

    /** @var bool Status for display */
    public $active = 1;

    /** @var int category position */
    public $position;

    /** @var mixed string or array of Description */
    public $description;

    /** @var int Parent category ID */
    public $id_parent;

    /** @var int default Category id */
    public $id_category_default;

    /** @var int Parents number */
    public $level_depth;

    /** @var int Nested tree model "left" value */
    public $nleft;

    /** @var int Nested tree model "right" value */
    public $nright;

    /** @var mixed string or array of string used in rewrited URL */
    public $link_rewrite;

    /** @var mixed string or array of Meta title */
    public $meta_title;

    /** @var mixed string or array of Meta keywords */
    public $meta_keywords;

    /** @var mixed string or array of Meta description */
    public $meta_description;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var bool is Category Root */
    public $is_root_category;

    /** @var int */
    public $id_shop_default;

    public $groupBox;

    /** @var bool */
    public $doNotRegenerateNTree = false;

    protected static $_links = array();

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'category',
        'primary' => 'id_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'nleft' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'nright' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'level_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop_default' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_root_category' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        ),
    );

    /** @var string id_image is the category ID when an image exists and 'default' otherwise */
    public $id_image = 'default';

    

    /**
     * CategoryCore constructor.
     *
     * @param null $idCategory
     * @param null $idLang
     * @param null $idShop
     */
    public function __construct($idCategory = null, $idLang = null, $idShop = null)
    {
        parent::__construct($idCategory, $idLang, $idShop);
        $this->image_dir = _PS_CAT_IMG_DIR_;
        $this->id_image = ($this->id && file_exists($this->image_dir . (int) $this->id . '.jpg')) ? (int) $this->id : false;
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            $this->doNotRegenerateNTree = true;
        }
    }

 
    
    
    
   
    
    /**
     * Returns category products.
     *
     * @param int $idLang Language ID
     * @param int $p Page number
     * @param int $n Number of products per page
     * @param string|null $orderyBy ORDER BY column
     * @param string|null $orderWay Order way
     * @param bool $getTotal If set to true, returns the total number of results only
     * @param bool $active If set to true, finds only active products
     * @param bool $random If true, sets a random filter for returned products
     * @param int $randomNumberProducts Number of products to return if random is activated
     * @param bool $checkAccess If set to `true`, check if the current customer
     *                          can see the products from this category
     * @param Context|null $context Instance of Context
     *
     * @return array|int|false Products, number of products or false (no access)
     *
     * @throws PrestaShopDatabaseException
     */
    public function getProducts(
        $idLang,
        $p,
        $n,
        $orderyBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $random = false,
        $randomNumberProducts = 1,
        $checkAccess = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        /*if ($checkAccess && !$this->checkAccess($context->customer->id)) {
            return false;
        }*/

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $idSupplier = (int) Tools::getValue('id_supplier');

        /* Return only the number of products */
        if ($getTotal) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `' . _DB_PREFIX_ . 'product` p
					' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = ' . (int) $this->id .
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
                ($active ? ' AND product_shop.`active` = 1' : '') .
                ($idSupplier ? ' AND p.id_supplier = ' . (int) $idSupplier : '');

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $orderyBy = Validate::isOrderBy($orderyBy) ? Tools::strtolower($orderyBy) : 'position';
        $orderWay = Validate::isOrderWay($orderWay) ? Tools::strtoupper($orderWay) : 'ASC';

        $orderByPrefix = false;
        if ($orderyBy == 'id_product' || $orderyBy == 'date_add' || $orderyBy == 'date_upd') {
            $orderByPrefix = 'p';
        } elseif ($orderyBy == 'name') {
            $orderByPrefix = 'pl';
        } elseif ($orderyBy == 'manufacturer' || $orderyBy == 'manufacturer_name') {
            $orderByPrefix = 'm';
            $orderyBy = 'name';
        } elseif ($orderyBy == 'position') {
            $orderByPrefix = 'cp';
        }

        if ($orderyBy == 'price') {
            $orderyBy = 'orderprice';
        }

        $nbDaysNewProduct = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . (Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . ', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int) $nbDaysNewProduct . ' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `' . _DB_PREFIX_ . 'category_product` cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p
					ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') .
                (Combination::isFeatureActive() ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = ' . (int) $idLang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = ' . (int) $context->shop->id . '
					AND cp.`id_category` = ' . (int) $this->id
                    . ($active ? ' AND product_shop.`active` = 1' : '')
                    . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    . ($idSupplier ? ' AND p.id_supplier = ' . (int) $idSupplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT ' . (int) $randomNumberProducts;
        } else {
            $sql .= ' ORDER BY ' . (!empty($orderByPrefix) ? $orderByPrefix . '.' : '') . '`' . bqSQL($orderyBy) . '` ' . pSQL($orderWay) . '
			LIMIT ' . (((int) $p - 1) * (int) $n) . ',' . (int) $n;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($orderyBy == 'orderprice') {
            Tools::orderbyPrice($result, $orderWay);
        }

        // Modify SQL result
        return $result;
        //return \Product::getProductsProperties($idLang, $result);
    }

    
}
