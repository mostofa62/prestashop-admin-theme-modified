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

namespace Bannermanager\Model;

/**
 * Class Banner.
 */
class Banner extends \ObjectModel
{
    /**
     * @var int
     */
    public $id_banner;

    /**
     * @var string
     */
    public $title;

     /**
     * @var string
     */
    public $description;

    public $image_name;

    public $is_active;

   

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'arkylus_bannermanager',
        'primary' => 'id_banner',
        //'multilang' => true,
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true, 'size' => 100),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'size' => 150),
            'image_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => false, 'size' => 64)
            ),
            'is_active'=>array('type' => self::TYPE_INT, 'lang' => true, 'required' => false, 'size' => 1),
            
    );
    

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        
    }
    /*
    public function add($auto_date = true, $null_values = false)
    {
        if (is_array($this->content)) {
            $this->content = json_encode($this->content);
        }

        if (!$this->position) {
            $this->position = 1;
        }

        $return = parent::add($auto_date, $null_values);
        $this->content = json_decode($this->content, true);

        return $return;
    }

    public function update($auto_date = true, $null_values = false)
    {
        if (is_array($this->content)) {
            $this->content = json_encode($this->content);
        }

        $return = parent::update($auto_date, $null_values);
        $this->content = json_decode($this->content, true);

        return $return;
    }
    */

    public function toArray()
    {
        return [            
            'id_banner' => $this->id_banner,
            'title' => $this->title,
            'description' => $this->description,
            'image_name'=>$this->image_name,
            'is_active'=>$this->is_active            
        ];
    }
}
