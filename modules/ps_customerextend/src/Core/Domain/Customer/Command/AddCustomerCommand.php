<?php
/**
override customer command by Arkylus, Mostofa
 */

namespace Customerextend\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use Customerextend\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

/**
 * Adds new customer with provided data
 */
class AddCustomerCommand
{
    

    /**
     * @var Name
     */
    private $name;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var Password
     */
    private $password;

    /**
     * @var int
     */
    private $defaultGroupId;

    /**
     * @var int[]
     */
    private $groupIds;
    

    /**
     * @var bool
     */
    private $isEnabled;
    

    /**
     * @var int
     */
    private $shopId;

    

    

    /*mostofa*/

    private $mobileNo;

    
    public function __construct(
        $name,        
        $email,
        $password,
        $defaultGroupId,
        array $groupIds,
        $shopId,        
        $isEnabled = true,       
        $mobileNo = null
    ) {
        $this->name = $name;        
        $this->email = new Email($email);
        $this->password = new Password($password);
        $this->defaultGroupId = $defaultGroupId;
        $this->groupIds = $groupIds;
        $this->shopId = $shopId;
        
        $this->isEnabled = $isEnabled;       

        $this->mobileNo = $mobileNo;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @return int[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    

    

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    //mostofa
    public function getMobileNo(){
        return $this->mobileNo;
    }

    
}
