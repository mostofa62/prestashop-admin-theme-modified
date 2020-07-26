<?php


namespace Bannermanager\Repository;
use Context;

class BannermanagerLegacyRepository {

    

    
   

    public function findImageByBanner($bannerId)
    {
        
        $db = \Db::getInstance();
        $request = 'SELECT `image_name` FROM `' . _DB_PREFIX_ . 'arkylus_bannermanager` WHERE id_banner='.pSQL($bannerId);
       

        return $db->getValue($request);
    }


    public function findImageSingleInfoByBanner($bannerId)
    {

    	        
        $db = \Db::getInstance();
        $request = 'SELECT * FROM `' . _DB_PREFIX_ . 'arkylus_banners` WHERE id_banner='.pSQL($bannerId).' AND is_active = 1';
        
        $row = $db->getRow($request);


        //return $db->getValue($request);
        $row['home_url']=$this->generateLinkBasedOnType($row['link_type'],$row['link_id']);
        return $row;
    }

    public function findImageChildInfoByBanner($bannerId)
    {

    	$data=[];        
        $db = \Db::getInstance();
        $request = 'SELECT * FROM `' . _DB_PREFIX_ . 'arkylus_banners` WHERE id_parent='.pSQL($bannerId).' AND is_active = 1';
        
        $row = $db->executeS($request);
        foreach ($row as $k=>$v) {
        	$data[$k]=$v+['home_url'=>$this->generateLinkBasedOnType($v['link_type'],$v['link_id'])];

        }

        return $data;
    }

    private function generateLinkBasedOnType($link_type, $link_id=null){
    	$link = null;

    	if($link_type > 0){
    		$link_id = (int) $link_id;
    		//CMS PAGE -1
    		if($link_type == 1){
    			$link = Context::getContext()->link->getCMSLink( $link_id ); 
    			/*
    			$id_lang = (int) \Configuration::get('PS_LANG_DEFAULT');
    			$cms = new \CMS($link_id, $id_lang);
    			$link = $cms->link_rewrite;
    			*/
    		}
    		//CATEGORY ==2
    		elseif($link_type == 2){
    			$link = Context::getContext()->link->getCategoryLink( $link_id ); 
    		}
    		//PRODUCT ==3
    		elseif($link_type == 3){
    			$link = Context::getContext()->link->getProductLink( $link_id ); 
    		}
    		//BRAND OR MANUFACTURER ==4
    		elseif($link_type == 4){
    			$link = Context::getContext()->link->getManufacturerLink( $link_id ); 
    		}

    	}
    	//Static Link
    	else{
    		$link = Context::getContext()->link->getPageLink( "$link_id" );
    	}

    	return $link;

    }


    





}
