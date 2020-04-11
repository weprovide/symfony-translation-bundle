<?php

namespace WeProvide\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TranslationMessageLocaleAdmin extends AbstractAdmin
{
    // Define association many to one
    protected $parentAssociationMapping = 'TranslationMessage';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('list');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('locale', null, [
                'label' => false,
                'attr'  => [
                    'readonly' => true,
                ],
            ])
            ->add('value', null, [
                'label' => false,
                'attr'  => [
                    'class' => 'ckeditor',
                ],
            ]);
    }
}
