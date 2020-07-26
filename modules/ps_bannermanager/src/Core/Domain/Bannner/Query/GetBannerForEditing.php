<?php

namespace Bannermanager\Core\Domain\Bannner\Query;

use Bannermanager\Core\Domain\Banner\ValueObject\BannerId;

class GetBannerForEditing
{
	
	
    private $bannerId;

    
    public function __construct($bannerId)
    {
        $this->bannerId = new BannerId($bannerId);
    }

   
    public function getBannerId()
    {
        return $this->bannerId;
    }
}