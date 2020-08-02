/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import 'expose-loader?Tether!tether';
import 'bootstrap/dist/js/bootstrap.min';
import 'flexibility';
import 'bootstrap-touchspin';
import 'jquery-touchswipe';

import './responsive';
import './checkout';
import './customer';
import './listing';
import './product';
import './cart';



import DropDown from './components/drop-down';
import Form from './components/form';
import ProductMinitature from './components/product-miniature';
import ProductSelect from './components/product-select';
import TopMenu from './components/top-menu';

import prestashop from 'prestashop';
import EventEmitter from 'events';

import './lib/bootstrap-filestyle.min';
import './lib/jquery.scrollbox.min';

import './components/block-cart';
import $ from "jquery";

/** custom mostofa **/
import 'owl.carousel';
// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
  prestashop[i] = EventEmitter.prototype[i];
}

$(document).ready(() => {
  let dropDownEl = $('.js-dropdown');
  const form = new Form();
  let topMenuEl = $('.js-top-menu ul[data-depth="0"]');
  let dropDown = new DropDown(dropDownEl);
  let topMenu = new TopMenu(topMenuEl);
  let productMinitature = new ProductMinitature();
  let productSelect  = new ProductSelect();
  dropDown.init();
  form.init();
  topMenu.init();
  productMinitature.init();
  productSelect.init();

  $('.carousel[data-touch="true"]').swipe({
    swipe(event, direction, distance, duration, fingerCount, fingerData) {
      if (direction == 'left') {
        $(this).carousel('next');
      }
      if (direction == 'right') {
        $(this).carousel('prev');
      }
    },
    allowPageScroll: 'vertical',
  });

  //custom by mostofa
  var owl = $('.owl-carousel');
  owl.owlCarousel({
      loop:true,
      nav:true,
      //center:true,
      //autoWidth:true,
      stagePadding: 0,
      navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
      margin:0,
      responsive:{
          0:{
              items:1              
          },
          600:{
              items:3
          },            
          960:{
              items:4
          },
          1200:{
              items:4
          }
      }
  });
  owl.on('mousewheel', '.owl-stage', function (e) {
      if (e.deltaY>0) {
          owl.trigger('next.owl');
      } else {
          owl.trigger('prev.owl');
      }
      e.preventDefault();
  });

  //end mostofa custom

});
