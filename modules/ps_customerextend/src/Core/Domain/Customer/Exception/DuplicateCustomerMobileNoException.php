<?php


namespace Customerextend\Core\Domain\Customer\Exception;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Exception is thrown when email which already exists is being used to create or update other customer
 */
class DuplicateCustomerMobileNoException extends CustomerException
{
    
	/**
     * @var Email
     */
    private $mobileNo;

    /**
     * @param Email $email
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($mobileNo, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->mobileNo = $mobileNo;
    }

    /**
     * @return Email
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

}
