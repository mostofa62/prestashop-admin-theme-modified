<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerOtpForm extends AbstractForm
{
    private $context;
    private $urls;

    private $show_otp_input=false;

    protected $template = 'checkout/_partials/otp-form.tpl';

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerOtpFormatter $formatter,
        array $urls
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->context = $context;
        $this->translator = $translator;
        $this->formatter = $formatter;
        $this->urls = $urls;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
    }

    public function submit()
    {
        if ($this->validate()) {
            Hook::exec('actionAuthenticationBefore');
            $otp = $this->getValue('otp');
            $mobile_no = $this->getValue('mobile_no');
            $customerOtp = new CustomerOtp($mobile_no);
            
            //$this->setValue('mobile_no', $authentication->mobile_no);

            if (isset($customerOtp) && isset($customerOtp->password) &&
                $customerOtp->password != $otp) {
                $this->errors[''][] = $this->translator->trans("Code mismatch for this mobile no: {$mobile_no}, please check again.", [], 'Shop.Notifications.Error');
                $this->show_otp_input=true;
            } else{

                //$this->context->updateCustomer(null);

                $customer = new Customer();
                $authentication = $customer->getByMobileNo(
                    $mobile_no               
                );

                //var_dump($customer);die();

                $this->context->updateCustomer($customer);
                Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
                
                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
            }
        }

        return !$this->hasErrors();
    }

    /*public function submit()
    {
        
        return false;
        if ($this->validate()) {
            //Hook::exec('actionAuthenticationBefore');

            $customer = new Customer();
            $authentication = $customer->getByMobileNo(
                $this->getValue('mobile_no')                
            );
            //$this->setValue('mobile_no', $authentication->mobile_no);

            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[''][] = $this->translator->trans('Your account isn\'t available at this time, please contact us', [], 'Shop.Notifications.Error');
            } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                $this->errors[''][] = $this->translator->trans('Authentication failed, account is not available please create one.', [], 'Shop.Notifications.Error');
            } else {

                
                //$this->context->smarty->assign('show_otp_input',true);
                $found = CustomerOtp::getByMobileNo($this->getValue('mobile_no'));

                $customerOtp = $found? new CustomerOtp($this->getValue('mobile_no')): new CustomerOtp();

                //var_dump($customerOtp);die();

                $customerOtp->mobile_no = $this->getValue('mobile_no');
                $customerOtp->password = rand(111111,666666);
                $customerOtp->is_sent = 0;
                
                if($found){
                    $customerOtp->update();
                    
                }else{
                    $customerOtp->add();
                }
                
                
            
                //$this->context->updateCustomer($customer);

                //Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
                /*
                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
                
            }
        }

        return !$this->hasErrors();
    }*/

    public function getTemplateVariables()
    {
        if (!$this->formFields) {
            $this->formFields = $this->formatter->getFormat();
        }

        return [
            'action' => $this->action,
            'urls' => $this->urls,
            'formFields' => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            ),
            'errors' => $this->getErrors(),
            'show_otp_input'=>$this->show_otp_input            
        ];
    }
}
