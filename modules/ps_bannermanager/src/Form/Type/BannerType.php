<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Bannermanager\Form\Type;

use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcher;
use Symfony\Component\Form\FormEvents;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
class BannerType extends TranslatorAwareType
{

   public $hookDispatcher;
   
   public function __construct(
        TranslatorInterface $translator,
        array $locales,
        HookDispatcher $hookDispatcher      
    ) {
        parent::__construct($translator, $locales);
       $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_banner', HiddenType::class)
            ->add('title', TextType::class, [
                //'locales' => $this->locales,
                'required' => true,
                'label' => $this->trans('Title', 'Module.Bannermanager.Title'),
            ])
            ->add('description', TextType::class, [
                //'locales' => $this->locales,
                'required' => false,
                'label' => $this->trans('Description', 'Module.Bannermanager.Description'),
            ])
            ->add('image_name', HiddenType::class)
            ->add('is_active', SwitchType::class, [
            // Customized choices with ON/OFF instead of Yes/No
                'choices' => [
                    'OFF' => 0,
                    'ON' => 1,
                ],
                'label' => $this->trans('Active', 'Module.Bannermanager.Active'),
            ])
        ;
        /*
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $event->setData($data); 
            
        });*/
        //$builder->setData($builder->getData());
        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminBannerManager',
            ['form_builder' => &$builder,'form_data'=>$builder->getData()]
        );

        


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'module_banner';
    }
}
