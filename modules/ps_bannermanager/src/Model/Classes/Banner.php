<?php

namespace Bannermanager\Model\Classes;

class Banner extends \ObjectModel
{
	
	public $id;

	public $title;

    public $description;

    public $image_name;

    public $link_type;

    public $link_id;

	public $is_active;

	public $is_show_title;

	public $is_single;

    public $id_parent;

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'arkylus_banners',
        'primary' => 'id_banner',        
        'fields' => array(
            'title' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 70),
            'description' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 100),
            'image_name' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 64),
            'link_type' => array('type' => self::TYPE_INT, 'size' => 2),
            'link_id' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 20),
            'is_active' => array('type' => self::TYPE_BOOL),
            'is_show_title' => array('type' => self::TYPE_BOOL),
            'is_single' => array('type' => self::TYPE_BOOL),
            'id_parent' => array('type' => self::TYPE_INT, 'size' => 10,'required' => false),           
        ),
    );
}