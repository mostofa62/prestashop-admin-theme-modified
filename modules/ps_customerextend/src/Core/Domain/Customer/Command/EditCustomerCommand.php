<?php


namespace Customerextend\Core\Domain\Customer\Command;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use Customerextend\Core\Domain\Customer\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;

/**
 * Edits provided customer.
 * It can edit either all or partial data.
 *
 * Only not-null values are considered when editing customer.
 * For example, if the email is null, then the original value is not modified,
 * however, if email is set, then the original value will be overwritten.
 */
class EditCustomerCommand
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var Name|null
     */
    private $name;
    

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @var Password|null
     */
    private $password;

    /**
     * @var int|null
     */
    private $defaultGroupId;

    /**
     * @var int[]|null
     */
    private $groupIds;

    

    /**
     * @var bool
     */
    private $isEnabled;    

    /*mostofa*/

    private $mobileNo;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return FirstName|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $firstName
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }    

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return Password|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = new Password($password);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDefaultGroupId()
    {
        return $this->defaultGroupId;
    }

    /**
     * @param int $defaultGroupId
     *
     * @return self
     */
    public function setDefaultGroupId($defaultGroupId)
    {
        $this->defaultGroupId = $defaultGroupId;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return $this->groupIds;
    }

    /**
     * @param int[] $groupIds
     *
     * @return self
     */
    public function setGroupIds(array $groupIds)
    {
        $this->groupIds = $groupIds;

        return $this;
    }

    

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     *
     * @return self
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    
    //mostofa
    public function getMobileNo(){
        return $this->mobileNo;
    }

    public function setMobileNo($mobileNo){
        $this->mobileNo = $mobileNo;
        return $this;
    }
}
