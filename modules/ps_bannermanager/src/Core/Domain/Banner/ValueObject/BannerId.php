<?php

namespace Bannermanager\Core\Domain\Banner\ValueObject;


class BannerId
{
	
	private $id;

  
    public function __construct($id)
    {        
        $this->id = $id;
    }


    public function getValue()
    {
        return $this->id;
    }
}