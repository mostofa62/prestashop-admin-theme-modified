<?php

use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerPersisterCore
{
    private $errors = [];
    private $context;
    private $crypto;
    private $translator;
    private $guest_allowed;

    public function __construct(
        Context $context,
        Crypto $crypto,
        TranslatorInterface $translator,
        $guest_allowed
    ) {
        $this->context = $context;
        $this->crypto = $crypto;
        $this->translator = $translator;
        $this->guest_allowed = $guest_allowed;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function save(Customer $customer, $clearTextPassword, $newPassword = '', $passwordRequired = true)
    {
        if ($customer->id) {
            return $this->update($customer, $clearTextPassword, $newPassword, $passwordRequired);
        } else {
            return $this->create($customer, $clearTextPassword);
        }
    }

    private function update(Customer $customer, $clearTextPassword, $newPassword, $passwordRequired = true)
    {
        if (!$customer->is_guest && $passwordRequired && !$this->crypto->checkHash(
            $clearTextPassword,
            $customer->passwd,
            _COOKIE_KEY_
        )) {
            $msg = $this->translator->trans(
                'Invalid email/password combination',
                [],
                'Shop.Notifications.Error'
            );
            $this->errors['email'][] = $msg;
            $this->errors['password'][] = $msg;

            return false;
        }

        if (!$customer->is_guest) {
            $customer->passwd = $this->crypto->hash(
                $newPassword ? $newPassword : $clearTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest || !$passwordRequired) {
            // TODO SECURITY: Audit requested
            if ($customer->id != $this->context->customer->id) {
                
                $this->errors['email'][] = $this->translator->trans(
                    'There seems to be an issue with your account, please contact support',
                    [],
                    'Shop.Notifications.Error'
                );

                return false;
            }
        }

        $guest_to_customer = false;

        if ($clearTextPassword && $customer->is_guest) {
            $guest_to_customer = true;
            $customer->is_guest = false;
            $customer->passwd = $this->crypto->hash(
                $clearTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest || $guest_to_customer) {
            // guest cannot update their email to that of an existing real customer
            if (Customer::customerExists($customer->email, false, true)) {
                $this->errors['email'][] = $this->translator->trans(
                    'An account was already registered with this email address',
                    [],
                    'Shop.Notifications.Error'
                );

                return false;
            }
        }

        if ($customer->email != $this->context->customer->email) {
            $customer->removeResetPasswordToken();
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            Hook::exec('actionCustomerAccountUpdate', [
                'customer' => $customer,
            ]);
            
        }

        return $ok;
    }

    private function create(Customer $customer, $clearTextPassword)
    {
        if (!$clearTextPassword) {
            if (!$this->guest_allowed) {
                $this->errors['password'][] = $this->translator->trans(
                    'Password is required',
                    [],
                    'Shop.Notifications.Error'
                );

                return false;
            }

            
            $clearTextPassword = $this->crypto->hash(
                microtime(),
                _COOKIE_KEY_
            );

            $customer->is_guest = true;
        }

        $customer->passwd = $this->crypto->hash(
            $clearTextPassword,
            _COOKIE_KEY_
        );

        if (Customer::customerExists($customer->email, false, true)) {
            $this->errors['email'][] = $this->translator->trans(
                'An account was already registered with this email address',
                [],
                'Shop.Notifications.Error'
            );

            return false;
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            
            Hook::exec('actionCustomerAccountAdd', array(
                'newCustomer' => $customer,
            ));
        }

        return $ok;
    }

}
