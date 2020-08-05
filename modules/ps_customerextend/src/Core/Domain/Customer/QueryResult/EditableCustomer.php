<?php


namespace Customerextend\Core\Domain\Customer\QueryResult;


use Customerextend\Core\Domain\Customer\ValueObject\CustomerId;
use Customerextend\Core\Domain\Customer\ValueObject\Email;

/**
 * Stores editable data for customer
 */
class EditableCustomer
{
    /**
     * @var CustomerId
     */
    private $customerId;

    
    /**
     * @var string
     */
    private $name;
   

    /**
     * @var string
     */
    private $email;

    

    /**
     * @var bool
     */
    private $isEnabled;

    

    /**
     * @var array|int[]
     */
    private $groupIds;

    /**
     * @var int
     */
    private $defaultGroupId;


    /*mostofa*/

    private $mobileNo;

    
    public function __construct(
        CustomerId $customerId,
        $name,        
        Email $email,       
        $isEnabled,        
        array $groupIds,
        $defaultGroupId,        
        $mobileNo
    ) {
        $this->customerId = $customerId;        
        $this->name = $name;       
        $this->email = $email;        
        $this->isEnabled = $isEnabled;        
        $this->groupIds = $groupIds;
        $this->defaultGroupId = $defaultGroupId;       
        $this->mobileNo = $mobileNo;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

   

    /**
     * @return FirstName
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

   

    /**
     * @return array|int[]
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @return int
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

   

    
    //mostofa
    public function getMobileNo(){
        return $this->mobileNo;
    }
}
