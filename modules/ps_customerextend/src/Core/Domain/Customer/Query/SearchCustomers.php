<?php


namespace Customerextend\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Searchers for customers by phrases matching customer's first name, last name, email and id
 */
class SearchCustomers
{
    /**
     * @var string[]
     */
    private $phrases;

    /**
     * @param string[] $phrases
     */
    public function __construct(array $phrases)
    {
        $this->assertPhrasesAreNotEmpty($phrases);

        $this->phrases = $phrases;
    }

    /**
     * @return string[]
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
     * @param string[] $phrases
     */
    private function assertPhrasesAreNotEmpty(array $phrases)
    {
        if (empty($phrases)) {
            throw new CustomerException('Phrases cannot be empty when searching customers.');
        }
    }
}
