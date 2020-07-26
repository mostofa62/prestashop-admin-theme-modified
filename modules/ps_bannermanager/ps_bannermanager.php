<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}


if (!defined('_PS_BANNER_IMG_DIR_')) {
    define('_PS_BANNER_IMG_DIR_', _PS_IMG_DIR_.'bannermanager/');
}

if (!defined('_PS_BANNER_IMG_URI_')) {
    //define('_PS_BANNER_IMG_URI_', '/img/bannermanager/');
    define('_PS_BANNER_IMG_URI_', 'img/bannermanager/');
}

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Bannermanager\Form\Type\CustomContentType;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Ps_Bannermanager extends Module implements WidgetInterface
{
    
    //private const _PS_BANNER_IMG_DIR_ = '/img/bannermanager/';

    public $templateFile;

    private $repository;

    public function __construct()
    {
        $this->name = 'ps_bannermanager';
        $this->version = '1.0.0';
        $this->author = 'Golam Mostofa';
        $this->need_instance = 0;
        $this->tab = 'front_office_features';

        $this->tabs = [
            [
                'class_name' => 'BannerManager',
                'visible' => true,
                'name' => 'Banner Manager',
                'parent_class_name' => 'AdminParentThemes',
            ],
        ];

        
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Arkylus BannerManager');

        $this->description = $this->l('BannerManager By Arkylus, Can Manage Multiple Banner with title and simple description');

        $this->secure_key = Tools::encrypt($this->name);

        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        //$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->templateFile = 'module:ps_bannermanager/views/templates/hook/banner.tpl';

    }

    /**
     * This function is required in order to make module compatible with new translation system.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if ( $this->installTables() && 
            $this->installTab() &&
                $this->registerHook('actionAdminBannerManager')
                ) { 
            return true; 
        }
        
        return false;
    }

    public function uninstall()
    {
        return parent::uninstall() && 
        $this->uninstallTables() &&
        $this->uninstallModuleTab();
    }

    public function installTab()
    {
        
        $tab = new Tab(); 
        $tab->active = 1;       
        $tab->class_name = 'BannerManager';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Banner Manager';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentThemes');
        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallModuleTab(){

        $id_tab = Tab::getIdFromClassName("BannerManager");
        if($id_tab){
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink("BannerManager")
        );
    }

   

    /**
     * Installs sample tables required for demonstration.
     *
     * @return bool
     */
    private function installTables()
    {
        
        

        $db = Db::getInstance();

        $engine =pSQL(_MYSQL_ENGINE_);
        $db_prefix =pSQL(_DB_PREFIX_);

        $sql = "CREATE TABLE IF NOT EXISTS `{$db_prefix}arkylus_banners`(
                `id_banner` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(100) NULL,
                `description` VARCHAR(150) NULL,
                `image_name` varchar(64) NULL,
                `link_type` TINYINT(2) NULL,
                `link_id` INT(10) UNSIGNED NULL,                
                `is_active` TINYINT(1) NOT NULL DEFAULT 0,
                `is_show_title` TINYINT(1) NOT NULL DEFAULT 0,
                `is_single` TINYINT(1) NOT NULL DEFAULT 1,
                `id_parent` INT(10) UNSIGNED NULL,              
                PRIMARY KEY (`id_banner`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8";

        return $db->execute($sql);

        /*$success = true;       

        $queries = [

             "CREATE TABLE IF NOT EXISTS `{$db_prefix}arkylus_bannermanager` (
                `id_banner` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(100) NULL,
                `description` VARCHAR(150) NULL,
                `image_name` varchar(64) NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_banner`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `{$db_prefix}arkylus_banners`(
                `id_banner` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(100) NULL,
                `image_name` varchar(64) NULL,
                `link_type` TINYINT(2) NULL,
                `link_id` INT(10) UNSIGNED NULL,                
                `is_active` TINYINT(1) NOT NULL DEFAULT 0,
                `is_show_title` TINYINT(1) NOT NULL DEFAULT 0,
                `is_single` TINYINT(1) NOT NULL DEFAULT 1,
                `id_parent` INT(10) UNSIGNED NULL ,              
                PRIMARY KEY (`id_banner`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `{$db_prefix}arkylus_banners_info`(
                `id_banner_info` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_banner` int(10) unsigned NOT NULL,
                `title` VARCHAR(100) NULL,
                `description` VARCHAR(150) NULL,
                `image_name` varchar(64) NULL,
                `link_type` TINYINT(2) NULL,
                `link_id` INT(10) UNSIGNED NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 0,
                `is_show_title` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_banner_info`)
            ) ENGINE=$engine DEFAULT CHARSET=utf8"           
        ];
        foreach ($queries as $query) {
            $success &= $db->execute($query);
        }

        return $success;*/
    }

    /**
     * Uninstalls sample tables required for demonstration.
     *
     * @return bool
     */
    private function uninstallTables()
    {
        $db = Db::getInstance();
        $db_prefix =pSQL(_DB_PREFIX_); 
        $sql = "DROP TABLE IF EXISTS `{$db_prefix}arkylus_banners`";
        /*$sql = "DROP TABLE IF EXISTS
            `{$db_prefix}arkylus_bannermanager`,
            `{$db_prefix}arkylus_banners`,
            `{$db_prefix}arkylus_banners_info`";*/

        return $db->execute($sql);
    }
    /*
    private function createBannerImgDirector(){
        return makedirs(_PS_BANNER_IMG_DIR_);
    }


    private function makedirs($dirpath, $mode=0755) {
        return is_dir($dirpath) || mkdir($dirpath, $mode, true);
    }*/


    public function hookActionAdminBannerManager($params){

        /** @var bannerImageRepository $bannerImageRepository */
        $bannerImageRepository = $this->get(
            'bannermanager.repository'
        );

        $translator = $this->getTranslator();
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];

        $formBuilder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            
            $data = $event->getData();
            //var_dump($data);die();
            $data['image_name'] = $this->uploadImage($data);
            $form = $event->getForm();
            $event->setData($data); 
            
            
        });

        //var_dump($params['form_data']);
        //die();
        // we add to the Symfony form an `upload_image_file` field that will be used by BO user to upload image files
        $formBuilder
            ->add('upload_image_file', FileType::class, [
                'label' => $translator->trans('Upload Banner', [], 'Modules.Bannermanager.Banner'),
                'required' => false,
            ]);

        //var_dump($params); die();

        /** @var SupplierExtraImage $supplierExtraImage */
        if( isset($params['form_data']['id_banner']) 
            && $id_banner = (int) $params['form_data']['id_banner']  ){
        
        $bannerImage = $bannerImageRepository->findImageByBanner($id_banner);
        //var_dump(file_exists(_PS_BANNER_IMG_DIR_ . $bannerImage));die();

            if (isset($bannerImage) && file_exists(_PS_BANNER_IMG_DIR_ . $bannerImage)) {
                // When an image is already registered for this supplier, we add to the Symfony an
                // 'image_file' to provide a preview input to BO user and also provide a "delete button"
                //var_dump($bannerImage);die();
                /*
                $formBuilder
                ->add('image_file', FileType::class, [
                    'label' => $translator->trans('Upload Banner Text', [], 'Modules.Bannermanager.Banner'),
                    'required' => false,
                ]);*/
                
                $formBuilder
                    ->add('image_file', CustomContentType::class, [
                        'required' => false,
                        'template' => '@Modules/ps_bannermanager/views/templates/admin/list_banner/upload_image.html.twigs',
                        'data' => [
                            'bannerId' => $id_banner,
                            'imageUrl' => _PS_BANNER_IMG_DIR_ . $bannerImage,
                        ],
                    ]);
            }
        }
        

        
    }

   

    private function uploadImage(array $params)
    {
        /** @var ImageUploaderInterface $supplierExtraImageUploader */
        $bannerImageUploader = $this->get(
            'bannermanager.banner_image_uploader'
        );

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $params['upload_image_file'];

        if ($uploadedFile instanceof UploadedFile) {
            $bannerImageUploader->upload($params['id_banner'], $uploadedFile);
            if(isset($params['image_name'])){
                $bannerImageUploader->deleteOldImage($params['image_name']);
            }
            return $uploadedFile->getClientOriginalName();
        }

        return isset($params['image_name'])?$params['image_name']:null;
        
    }

    // widget to show data in front page

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function renderWidget($hookName, array $configuration){
        $key = 'ps_bannermanager';

        if (!$this->isCached($this->templateFile, $this->getCacheId($key))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
             $this->smarty->assign('banner_url',__PS_BASE_URI__._PS_BANNER_IMG_URI_);
             //$this->smarty->assign('banner_url',_PS_BANNER_IMG_URI_);
        }

        return $this->fetch($this->templateFile, $this->getCacheId($key));
    }

    public function getWidgetVariables($hookName, array $configuration){

        
        $banners = [
            'single_banner'=>null,            
            'child_banner'=>null,
            'space_class'=>null            
        ];
        //$id_hook = Hook::getIdByName($hookName);       
        $id_banner = (int)$configuration['id_banner'];
        
        
        
        $bannerImageRepository = $this->context->controller->getContainer()->get(
            'bannermanager.front.repository'
        );



        $singleBanner = $bannerImageRepository->findImageSingleInfoByBanner($id_banner);
        $banners['single_banner'] = isset($singleBanner)?$singleBanner:null;

        //var_dump($banners); die();
        
        if(isset($singleBanner) && isset($singleBanner['is_single'])  && (int)$singleBanner['is_single'] < 1 ){
            $childBanner = $bannerImageRepository->findImageChildInfoByBanner($id_banner);
            $cbg = $this->child_banner_generate($childBanner);
            $banners['child_banner'] = $cbg['child_banner'];
            $banners['space_class'] = $cbg['space_class'];
        }

        //var_dump($banners['child_banner']);die();
        
        //$bannerImage = $bannerImageRepository->findImageByBanner($id_banner);
        //$bannerImage = "3.jpg";

        //$link = $bannerImageRepository->generateLinkBasedOnType(4,1);
        //$link = $bannerImageRepository->generateLinkBasedOnType(0,"manufacturer");
        /*
        $banners = [            
            'id_banner'=>$id_banner,
            //'home_url'=>$this->context->link->getPageLink('index',true),
            'home_url'=>$link,
            /*
            'banner_url'=>Context::getContext()->link->getModuleLink('ps_bannermanager', 'banner', array('id_banner' => $id_banner))*/
            
            //'banner_url'=>__PS_BASE_URI__._PS_BANNER_IMG_URI_.$bannerImage          
        //];
        
    
        return $banners;

    }

    private function child_banner_generate($child_banner){
        
        $data = [
            'child_banner'=>null,
            'space_class'=>null,
        ];
        
        $quantity = count($child_banner);
        $chunk_size = 3;
        
        if($quantity > 0 && $quantity <= 3){
            $chunk_size = $quantity;
            $data['space_class'] = $quantity <=2?"col-md-6" :"col-md-4";
        }else{
            $chunk_size = 4;
            $data['space_class'] = 'col-md-3';
        }



        if($quantity > 0){

            $output = array_chunk($child_banner, $chunk_size);

            if(count($child_banner) % $chunk_size) {
                $leftovers = array_pop($output);
                $last      = array_pop($output);
                array_push($output, array_merge($last, $leftovers));
            }
            $data['child_banner'] = $output;
        }

        return $data;
    }   



    
}
