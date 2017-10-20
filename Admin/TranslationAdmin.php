<?php

namespace WeProvide\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class TranslationAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'we_provide_admin_translation';
    protected $baseRoutePattern = 'we_provide_admin_translation';
    protected $translationDomain = 'WeProvideTranslationBundle';

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('export');
    }



// TODO : add button to extract translations
//    public function configureActionButtons($action, $object = null)
//    {
//        $list = parent::configureActionButtons($action, $object);
//
//        $list['routes'] = array(
//            'template' =>  'WeProvideAdminBundle:PageAdmin:routes.html.twig',
//        );
//
//        return $list;
//    }



    public function toString($object)
    {
        return 'test';// $object instanceof Page ? $object->getRouteName() : 'Vertaling';
    }
}
