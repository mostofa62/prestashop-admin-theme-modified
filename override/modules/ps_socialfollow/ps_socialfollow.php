<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}


class Ps_SocialfollowOverride extends Ps_Socialfollow
{
   


    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $social_links = array();

        if ($sf_facebook = Configuration::get('BLOCKSOCIAL_FACEBOOK')) {
            $social_links['facebook'] = array(
                'label' => $this->trans('Facebook', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-facebook-f',
                'url' => $sf_facebook,
            );
        }

        if ($sf_twitter = Configuration::get('BLOCKSOCIAL_TWITTER')) {
            $social_links['twitter'] = array(
                'label' => $this->trans('Twitter', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-twitter',
                'url' => $sf_twitter,
            );
        }

        if ($sf_rss = Configuration::get('BLOCKSOCIAL_RSS')) {
            $social_links['rss'] = array(
                'label' => $this->trans('Rss', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-rss',
                'url' => $sf_rss,
            );
        }

        if ($sf_youtube = Configuration::get('BLOCKSOCIAL_YOUTUBE')) {
            $social_links['youtube'] = array(
                'label' => $this->trans('YouTube', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-youtube-play',
                'url' => $sf_youtube,
            );
        }

        if ($sf_pinterest = Configuration::get('BLOCKSOCIAL_PINTEREST')) {
            $social_links['pinterest'] = array(
                'label' => $this->trans('Pinterest', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-pinterest-p',
                'url' => $sf_pinterest,
            );
        }

        if ($sf_vimeo = Configuration::get('BLOCKSOCIAL_VIMEO')) {
            $social_links['vimeo'] = array(
                'label' => $this->trans('Vimeo', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-vimeo',
                'url' => $sf_vimeo,
            );
        }

        if ($sf_instagram = Configuration::get('BLOCKSOCIAL_INSTAGRAM')) {
            $social_links['instagram'] = array(
                'label' => $this->trans('Instagram', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-instagram',
                'url' => $sf_instagram,
            );
        }

        if ($sf_linkedin = Configuration::get('BLOCKSOCIAL_LINKEDIN')) {
            $social_links['linkedin'] = array(
                'label' => $this->trans('LinkedIn', array(), 'Modules.Socialfollow.Shop'),
                'class' => 'fa fa-linkedin',
                'url' => $sf_linkedin,
            );
        }

        return array(
            'social_links' => $social_links,
        );
    }

    
}
