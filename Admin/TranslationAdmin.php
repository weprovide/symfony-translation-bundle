<?php

namespace WeProvide\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TranslationAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'we_provide_admin_translation';
    protected $baseRoutePattern = 'we_provide_admin_translation';


    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('extract');
        $collection->remove('create');

//        $collection->remove('show');
//        $collection->remove('delete');
//        $collection->remove('export');
    }


    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
//        $listMapper
//            ->addIdentifier('route_name')
//            ->add('state', null, array('editable' => true))
//        ;

    }


    /**
     * @param      $action
     * @param null $object
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        // TODO: only add extract action when user is allowed to
        $list['extract'] = array(
            'template' => 'WeProvideTranslationBundle:TranslationAdmin:action_extract.html.twig',
        );

        return $list;
    }


    public function toString($object)
    {
        return 'test';
    }
}
