<?php


namespace Customerextend\Core\Domain\Customer\QueryResult;

/**
 * Class PersonalInformation holds personal customer information.
 */
class PersonalInformation
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $isGuest;

    /**
     * @var string
     */
    private $socialTitle;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $registrationDate;

    /**
     * @var string
     */
    private $lastUpdateDate;

    /**
     * @var string
     */
    private $lastVisitDate;

    /**
     * @var string
     */
    private $rankBySales;

    /**
     * @var string
     */
    private $shopName;

    /**
     * @var string
     */
    private $languageName;

    /**
     * @var Subscriptions
     */
    private $subscriptions;

    /**
     * @var bool
     */
    private $isActive;


    private $mobile_no;

    private $name;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param bool $isGuest
     * @param string $socialTitle
     * @param string $birthday
     * @param string $registrationDate
     * @param string $lastUpdateDate
     * @param string $lastVisitDate
     * @param string $rankBySales
     * @param string $shopName
     * @param string $languageName
     * @param Subscriptions $subscriptions
     * @param bool $isActive
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $isGuest,
        $socialTitle,
        $birthday,
        $registrationDate,
        $lastUpdateDate,
        $lastVisitDate,
        $rankBySales,
        $shopName,
        $languageName,
        Subscriptions $subscriptions,
        $isActive,
        $mobile_no,
        $name
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->isGuest = $isGuest;
        $this->socialTitle = $socialTitle;
        $this->birthday = $birthday;
        $this->registrationDate = $registrationDate;
        $this->lastUpdateDate = $lastUpdateDate;
        $this->lastVisitDate = $lastVisitDate;
        $this->rankBySales = $rankBySales;
        $this->shopName = $shopName;
        $this->languageName = $languageName;
        $this->subscriptions = $subscriptions;
        $this->isActive = $isActive;
        $this->mobile_no = $mobile_no;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return $this->isGuest;
    }

    /**
     * @return string
     */
    public function getSocialTitle()
    {
        return $this->socialTitle;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @return string
     */
    public function getLastUpdateDate()
    {
        return $this->lastUpdateDate;
    }

    /**
     * @return string
     */
    public function getLastVisitDate()
    {
        return $this->lastVisitDate;
    }

    /**
     * @return string
     */
    public function getRankBySales()
    {
        return $this->rankBySales;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @return string
     */
    public function getLanguageName()
    {
        return $this->languageName;
    }

    /**
     * @return Subscriptions
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    public function getMobileNo(){
        return $this->mobile_no;
    }

    public function getName(){
        return $this->name;
    }
}
