<?php



namespace Bannermanager\Form\IdentifiableObject\DataProvider;



use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Bannermanager\Core\Domain\Banner\Query\GetBannerForEditing;
use Bannermanager\Core\Domain\Banner\QueryResult\EditableBanner;

class BannerFormDataProvider implements FormDataProviderInterface
{
    
    private $bus;

     public function __construct(
        CommandBusInterface $bus        
    ) {
        $this->bus = $bus;       
    }


    public function getData($BannerId)
    {
        /** @var EditableBanner $editableBanner */
        $editableBanner = $this->bus->handle(new GetBannerForEditing((int) $BannerId));

        $data = [
            'title' => $editableBanner->getTitle(), 
            'description'=>  $editableBanner->getDescription(),        
            'is_active' => $editableBanner->isActive(),
            'image_name'=>$editableBanner->getImageName(),
            'is_show_title' => $editableBanner->isShowTitle(),
            'is_single' => $editableBanner->isSingle(),
            'id_parent' => $editableBanner->getParent(),
            'link_type' => $editableBanner->getLinkType(),
            'link_id' => $editableBanner->getLinkId(),
        ];

        

        return $data;
    }

    
    

    public function getDefaultData()
    {
        return [            
            'is_active'=>0,
            'is_show_title'=>0,
            'is_single'=>1,
            'id_parent'=>null,
            'link_id'=>null
        ];
    }
    

   

   
}
