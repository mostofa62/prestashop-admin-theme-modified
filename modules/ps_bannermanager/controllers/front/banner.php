<?php




class Ps_BannermanagerBannerModuleFrontController extends ModuleFrontController
{

	public $auth = false;

	public $ajax;

	/*

	public function initContent()
    {

    	parent::initContent();    	

    	ob_end_clean();
        header('Content-Type: application/json');
        die(json_encode([            
            'id_banner' =>Tools::getValue('id_banner'),
        ]));
    }*/

    public function display()
    {
        $this->ajax = 1;
        
        
        $bannerImageRepository = $this->get(
            'bannermanager.front.repository'
        );

       

        $this->ajaxDie(json_encode([            
            'id_banner' =>Tools::getValue('id_banner'),
        ]));
    }

	

}