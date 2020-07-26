<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/_partials/steps/personal-information.tpl';
    private $loginForm;
    private $otpForm;
    private $registerForm;    

    private $show_login_form = false;

    private $show_otp_form = true;    
    

    public function __construct(
        Context $context,
        TranslatorInterface $translator,        
        CustomerLoginForm $loginForm,
        CustomerForm $registerForm,
        CustomerOtpForm $otpForm        
    ) {
        parent::__construct($context, $translator);
        $this->loginForm = $loginForm;
        $this->registerForm = $registerForm;
        $this->otpForm = $otpForm;        
    }

    public function handleRequest(array $requestParameters = array())
    {
        // personal info step is always reachable
        $this->setReachable(true);
        


        $this->registerForm
            ->fillFromCustomer(
                $this
                    ->getCheckoutProcess()
                    ->getCheckoutSession()
                    ->getCustomer()
            );

        if (isset($requestParameters['submitCreate'])) {
            $this->registerForm->fillWith($requestParameters);
            if ($this->registerForm->submit()) {
                $this->setNextStepAsCurrent();
                $this->setComplete(true);
            } else {
                $this->setComplete(false);
                $this->setCurrent(true);
                $this->getCheckoutProcess()->setHasErrors(true)->setNextStepReachable();
            }
        } elseif (isset($requestParameters['submitLogin'])) {
            $this->loginForm->fillWith($requestParameters);
            if ($this->loginForm->submit()) {
                $this->setNextStepAsCurrent();
                $this->setComplete(true);
            } else {
                $this->getCheckoutProcess()->setHasErrors(true);
                $this->show_login_form = true;
            }
        } elseif (array_key_exists('login', $requestParameters)) {
            $this->show_login_form = true;
            $this->setCurrent(true);
        } 
        //mostofa
        elseif(isset($requestParameters['submitLoginOtp'])){

            $this->otpForm->fillWith($requestParameters);
            
            if($this->otpForm->submit()){
                
                $this->setNextStepAsCurrent();
                $this->setComplete(true);

            }else {
                $this->setCurrent(true);
                $this->setComplete(false);
                $this->getCheckoutProcess()->setHasErrors(true);
                $this->show_otp_form = true;                
            }

        }
        //end mostofa

        $this->logged_in = $this
            ->getCheckoutProcess()
            ->getCheckoutSession()
            ->customerHasLoggedIn();

        if ($this->logged_in && !$this->getCheckoutSession()->getCustomer()->is_guest) {
            $this->setComplete(true);
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Personal Information',
                array(),
                'Shop.Theme.Checkout'
            )
        );
    }

    public function render(array $extraParams = array())
    {
        


        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            array(
                'show_login_form' => $this->show_login_form,
                'login_form' => $this->loginForm->getProxy(),
                'register_form' => $this->registerForm->getProxy(),
                //mostofa
                'show_otp_form' => $this->show_otp_form,
                            
                'otp_form' => $this->otpForm->getProxy(),                
                //mostofa
                'guest_allowed' => $this->getCheckoutSession()->isGuestAllowed(),
                'empty_cart_on_logout' => !Configuration::get('PS_CART_FOLLOWING'),
            )
        );
    }
}
