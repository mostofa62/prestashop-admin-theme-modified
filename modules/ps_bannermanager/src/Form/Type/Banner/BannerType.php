<?php


namespace Bannermanager\Form\Type\Banner;


use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for manufacturer create/edit actions (Sell > Catalog > Brands & Suppliers)
 */
class BannerType extends TranslatorAwareType
{

	public function __construct(
        TranslatorInterface $translator,
        array $locales      
    ) {
        parent::__construct($translator, $locales);       
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_banner', HiddenType::class)
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 70,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 70]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'catalog_name',
                    ]),
                ],
                'required' => true,
                'label' => $this->trans('Title', 'Bannermanager.Banner.Title'),
            ])
            ->add('description', TextType::class, [
                'constraints' => [                   
                    new Length([
                        'max' => 150,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 150]
                        ),
                    ]),                    
                ],
                'required' => false,
                'label' => $this->trans('Description', 'Bannermanager.Banner.Description'),
            ])
            ->add('upload_image_file', FileType::class, [
            	'label' => $this->trans('Upload Image', 'Bannermanager.Banner.Image'),
                'required' => false,
            ])                
            ->add('image_name', HiddenType::class)
            ->add('link_type', ChoiceType::class, [
            // Customized choices with ON/OFF instead of Yes/No
                'choices' => [
                    'CMS PAGE' => 1,
                    'CATEGORY' => 2,
                    'PRODUCT' => 3,
                    'BRAND' => 4
                ],
                'label' => $this->trans('Link Type', 'Bannermanager.Banner.LinkType'),
                'required' => false,
            ])
            ->add('link_id', NumberType::class, [                
                'required' => false,
                'label' => $this->trans('Link Id', 'Bannermanager.Banner.LinkId'),
            ])
            ->add('is_active', SwitchType::class, [
            // Customized choices with ON/OFF instead of Yes/No
                'choices' => [
                    'No' => 0,
                    'Yes' => 1,
                ],
                'label' => $this->trans('Active', 'Bannermanager.Banner.Active'),
                'required' => false,
            ])
            ->add('is_show_title', SwitchType::class, [
            // Customized choices with ON/OFF instead of Yes/No
                'choices' => [
                    'No' => 0,
                    'Yes' => 1,
                ],
                'label' => $this->trans('Show Title', 'Bannermanager.Banner.ShowTitle'),
                'required' => false,
            ])
            ->add('is_single', SwitchType::class, [
            // Customized choices with ON/OFF instead of Yes/No
                'choices' => [
                    'No' => 0,
                    'Yes' => 1,
                ],
                'label' => $this->trans('Single Only', 'Bannermanager.Banner.SingleOnly'),
                'required' => false,
            ])
            ->add('id_parent', NumberType::class, [                
                'required' => false,
                'label' => $this->trans('Parent Id', 'Bannermanager.Banner.ParentId'),
            ])
        ;
        

        


    }


}