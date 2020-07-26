<?php


namespace Bannermanager\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Bannermanager\Model\Banner;
use Bannermanager\Repository\BannermanagerRepository;


final class BannermanagerFormDataHandler implements FormDataHandlerInterface
{
	
	private $repository;

	public function __construct(		
		BannermanagerRepository $repository
	){
		
		$this->repository = $repository;
	}
	
	

    public function create(array $data)
    {
        //var_dump($data);die();
        /*
        $bannerObjectModel = new Banner();
        // add data to object model
        // ...
        $bannerObjectModel->title = $data['title'];
        $bannerObjectModel->description = $data['description'];
        $bannerObjectModel->image_name = $data['image_name'];
        $bannerObjectModel->save();

        return $bannerObjectModel->id_banner;
        */
        
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
    	/*
        $bannerObjectModel = new Banner($id);
        // update data to object model
        $bannerObjectModel->title = $data['title'];
        $bannerObjectModel->description = $data['description'];
        $bannerObjectModel->image_name = $data['image_name'];
        $bannerObjectModel->update();
        */
       
        $this->repository->update($id, $data);
    }

    

}