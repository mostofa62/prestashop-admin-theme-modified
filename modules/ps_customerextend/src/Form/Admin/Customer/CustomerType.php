<?php


namespace Customerextend\Form\Admin\Customer;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Password;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Type is used to created form for customer add/edit actions
 */
class CustomerType extends AbstractType
{
    use TranslatorAwareTrait;

    
    private $groupChoices;

    

   
  

    /**
     * @param array $genderChoices
     * @param array $groupChoices
     * @param array $riskChoices
     * @param bool $isB2bFeatureEnabled
     * @param bool $isPartnerOffersEnabled
     */
    public function __construct(        
        array $groupChoices                
    ) {
        
        $this->groupChoices = $groupChoices;        
       
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => FirstName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => FirstName::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])           
            ->add('email', EmailType::class, [
                'constraints' => [
                    
                    new Email([
                        'message' => $this->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                    ]),
                ],
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Length([
                        'max' => Password::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => Password::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                        
                        'min' => Password::MIN_LENGTH,
                        'minMessage' => $this->trans(
                            'This field cannot be shorter than %limit% characters',
                            ['%limit%' => Password::MIN_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'required' => $options['is_password_required'],
            ])
            
            ->add('is_enabled', SwitchType::class, [
                'required' => false,
            ])
            
            ->add('group_ids', MaterialChoiceTableType::class, [
                'empty_data' => [],
                'choices' => $this->groupChoices,
            ])
            ->add('default_group_id', ChoiceType::class, [
                'required' => false,
                'placeholder' => null,
                'choices' => $this->groupChoices,
            ])
        ;

        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // password is configurable
                // so it may be optional when editing customer
                'is_password_required' => true,
            ])
            ->setAllowedTypes('is_password_required', 'bool')
        ;
    }
}
