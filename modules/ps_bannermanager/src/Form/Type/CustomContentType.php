<?php


namespace Bannermanager\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Type is used to add any content in any position of the form rather than actual field.
 */
final class CustomContentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['data'] = $options['data'];
        $view->vars['template'] = $options['template'];
        //var_dump($view->vars['template']);die();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'template',
            ])
            ->setDefaults([
                'required' => false,
                'data' => [],
            ])
            ->setAllowedTypes('template', 'string')
            ->setAllowedTypes('data', 'array')
        ;
    }
}
