<?php


namespace Customerextend\Core\Domain\Customer\QueryResult;

class Subscriptions
{
    /**
     * @var bool
     */
    private $isNewsletterSubscribed;

    /**
     * @var bool
     */
    private $isPartnerOffersSubscribed;

    /**
     * @param bool $isNewsletterSubscribed
     * @param bool $isPartnerOffersSubscribed
     */
    public function __construct($isNewsletterSubscribed, $isPartnerOffersSubscribed)
    {
        $this->isNewsletterSubscribed = $isNewsletterSubscribed;
        $this->isPartnerOffersSubscribed = $isPartnerOffersSubscribed;
    }

    /**
     * @return bool
     */
    public function isNewsletterSubscribed()
    {
        return $this->isNewsletterSubscribed;
    }

    /**
     * @return bool
     */
    public function isPartnerOffersSubscribed()
    {
        return $this->isPartnerOffersSubscribed;
    }
}
