<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
//use Symfony\Component\Validator\Constraints\Regex;

class Ps_CustomerExtend extends Module
{


	public function __construct()
    {
        $this->name = 'ps_customerextend';        
        $this->version = '1.0.0';
        $this->tab = 'administration';
        $this->author = 'Golam Mostofa';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6',
            'max' => _PS_VERSION_
        ];
        //$this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Arkylus Customer Extend');
        $this->description = $this->l('Add extra field, address at runtime.');

       

       
    }

    public function install()
    {
        return parent::install() &&
        $this->registerHook('actionCustomerGridDefinitionModifier') &&            
            $this->registerHook('actionCustomerGridQueryBuilderModifier') &&            
            $this->registerHook('actionCustomerFormBuilderModifier') &&
            $this->registerHook('actionAfterCreateCustomerFormHandler') &&
            $this->registerHook('actionAfterUpdateCustomerFormHandler') &&
            $this->alterCustomerTable()          
        ;

    }

    public function uninstall()
    {
        return parent::uninstall() &&
        $this->alterCustomerTableDrop();    
    }


    public function alterCustomerTable(){

    	$sql = '
            ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` ADD `mobile_no` VARCHAR(20) NULL AFTER email;
        ';

        return Db::getInstance()->execute($sql);
    }


    public function alterCustomerTableDrop(){

    	$sql = '
            ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` DROP COLUMN  `mobile_no`;
        ';

        return Db::getInstance()->execute($sql);
    }

    public function hookActionCustomerFormBuilderModifier(array $params)
	{
        
		$formBuilder = $params['form_builder'];
	    $formBuilder->add('mobile_no', TextType::class, [
            'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', [],'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 15,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',['%limit%' => 15],
                            'Admin.Notifications.Error'
                            
                        )
                    ]),
                    /*
                    new TypedRegex([
                        'type' => '^((016)|(017)|(018)|(015)|(013)|(014))$',
                    ]),
                    new Regex([
                        'pattern' => '/^((016)|(017)|(018)|(015)|(013)|(014))$/',
                        'match' => false,
                        'message' => 'Your name cannot contain a number',
                    ])*/
            ],
	        'label' => $this->l('Mobile number'),
	        'required' => true,
	    ]);

	}

	public function hookActionAfterUpdateCustomerFormHandler(array $params)
	{
	    //$this->updateCustomerMobileNo($params);
	}

	public function hookActionAfterCreateCustomerFormHandler(array $params)
	{
	    //$this->updateCustomerMobileNo($params);
	}




}