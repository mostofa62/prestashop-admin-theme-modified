<?php


namespace Bannermanager\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;

use Bannermanager\Core\Domain\Banner\Command\AddBannerCommand;
use Bannermanager\Core\Domain\Banner\Command\EditBannerCommand;
use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Bannermanager\Uploader\ImageUploaderDeleteInterface;
use Bannermanager\Uploader\BannerImageUploader;

final class BannerFormDataHandler implements FormDataHandlerInterface
{
	
	
    private $bus;
   
    private $imageUploader;


	public function __construct(
        CommandBusInterface $bus,
        ImageUploaderInterface $imageUploader
    ) {
        $this->bus = $bus;
        $this->imageUploader = $imageUploader;
    }
	
	

    public function create(array $data)
    {
        
        $image_name = null;
        /** @var UploadedFile $uploadedFlagImage */
        $uploadedLogo = $data['upload_image_file'];
        //var_dump( $uploadedLogo);die();

        if ($uploadedLogo instanceof UploadedFile) {
            $id = date('Ymdhis');
            $image_name = $id.'.'.$uploadedLogo->getClientOriginalExtension();
            $this->imageUploader->upload($id, $uploadedLogo);
        }

         /** @var BannerId $bannerId */
        $bannerId = $this->bus->handle(new AddBannerCommand(
            $data['title'],
            $data['description'],
            $data['is_active'],
            $image_name,//image name
            $data['is_show_title'],
            $data['is_single'],
            $data['id_parent'],
            $data['link_type'],
            $data['link_id']          
        ));

        

        return $bannerId->getValue();
    }

    public function update($bannerId, array $data)
    {
        /** @var UploadedFile $uploadedFlagImage */
        $uploadedLogo = $data['upload_image_file'];
        $image_name =  $data['image_name'];       

        if ($uploadedLogo instanceof UploadedFile) {
            $id = date('Ymdhis');
            $image_name = $id.'.'.$uploadedLogo->getClientOriginalExtension();
            $this->deleteOldImage($data['image_name']);
            $this->imageUploader->upload($id, $uploadedLogo);
            
        }

        $command = (new EditBannerCommand($bannerId))
            ->setTitle((string) $data['title'])
            ->setDescription((string)$data['description'])           
            ->setActive((int) $data['is_active'])
            ->setImageName((string) $image_name)
            ->setShowTitle((int) $data['is_show_title'])
            ->setSingle((int) $data['is_single'])
            ->setLinkType((int) $data['link_type'])
            ->setLinkId((int) $data['link_id'])
        
        
        ;

        

        $this->bus->handle($command);
    }

    public function deleteOldImage($bannerImage)
    {
        /** @var SupplierExtraImage $supplierExtraImage */       

        if (isset($bannerImage) && file_exists(_PS_BANNER_IMG_DIR_ . $bannerImage))
        {
        //var_dump(_PS_BANNER_IMG_DIR_ . $bannerImage);die();

            @unlink(_PS_BANNER_IMG_DIR_ . $bannerImage);
        }
    }

    

}