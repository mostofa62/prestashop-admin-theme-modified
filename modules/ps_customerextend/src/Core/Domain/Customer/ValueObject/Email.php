<?php


namespace Customerextend\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email as ValueObjectEmail;

class Email
{
    
	private $email;
	
    public function __construct($email)
    {
        

        $this->email = $email;
    }

    public function getValue()
    {
        return $this->email;
    }
}
