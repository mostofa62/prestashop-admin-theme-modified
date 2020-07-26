<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Store $object
 */
class AdminStoresController extends AdminStoresControllerCore
{

    public function __construct()
    {
        parent::__construct();
    }


    public function _getDefaultFieldsContent()
    {
        $this->context = Context::getContext();
        $countryList = array();
        $countryList[] = array('id' => '0', 'name' => $this->trans('Choose your country', array(), 'Admin.Shopparameters.Feature'));
        foreach (Country::getCountries($this->context->language->id) as $country) {
            $countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
        }
        $stateList = array();
        $stateList[] = array('id' => '0', 'name' => $this->trans('Choose your state (if applicable)', array(), 'Admin.Shopparameters.Feature'));
        foreach (State::getStates($this->context->language->id) as $state) {
            $stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);
        }

        $formFields = array(
            'PS_SHOP_NAME' => array(
                'title' => $this->trans('Shop name', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails and page titles.', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isGenericName',
                'required' => true,
                'type' => 'text',
                'no_escape' => true,
            ),            
            'PS_SHOP_EMAIL' => array('title' => $this->trans('Shop email', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in emails sent to customers.', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isEmail',
                'required' => true,
                'type' => 'text',
            ),
            'PS_SHOP_TITLE' => array(
                'title' => $this->trans('Shop Title', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Displayed in Top nav left side.', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isGenericName',                
                'type' => 'text',               
            ),
            'PS_SHOP_DETAILS' => array(
                'title' => $this->trans('Registration number', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Shop registration information (e.g. SIRET or RCS).', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isGenericName',
                'type' => 'textarea',
                'cols' => 30,
                'rows' => 5,
            ),
            'PS_SHOP_ADDR1' => array(
                'title' => $this->trans('Shop address line 1', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text',
            ),
            'PS_SHOP_ADDR2' => array(
                'title' => $this->trans('Shop address line 2', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isAddress',
                'type' => 'text',
            ),
            'PS_SHOP_CODE' => array(
                'title' => $this->trans('Zip/postal code', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ),
            'PS_SHOP_CITY' => array(
                'title' => $this->trans('City', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ),
            'PS_SHOP_COUNTRY_ID' => array(
                'title' => $this->trans('Country', array(), 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $countryList,
                'identifier' => 'id',
                'cast' => 'intval',
                'defaultValue' => (int) $this->context->country->id,
            ),
            'PS_SHOP_STATE_ID' => array(
                'title' => $this->trans('State', array(), 'Admin.Global'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $stateList,
                'identifier' => 'id',
                'cast' => 'intval',
            ),
            'PS_SHOP_PHONE' => array(
                'title' => $this->trans('Phone', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ),
            'PS_SHOP_FAX' => array(
                'title' => $this->trans('Fax', array(), 'Admin.Global'),
                'validation' => 'isGenericName',
                'type' => 'text',
            ),
        );

        return $formFields;
    }
     
}
