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

        // TODO: only add extract action when user is allowed to?
        $list['extract'] = array(
            'template' => 'WeProvideTranslationBundle:TranslationAdmin:action_extract.html.twig',
        );

        return $list;
    }


    public function toString($object)
    {
        return 'test';
    }


    public function getFilterParameters()
    {
        $parameters = array();

        // build the values array
        if ($this->hasRequest()) {
            $filters = $this->request->query->get('filter', array());

            // if persisting filters, save filters to session, or pull them out of session if no new filters set
            if ($this->persistFilters) {
                if ($filters == array() && $this->request->query->get('filters') != 'reset') {
                    $filters = $this->request->getSession()->get($this->getCode().'.filter.parameters', array());
                } else {
                    $this->request->getSession()->set($this->getCode().'.filter.parameters', $filters);
                }
            }

            $parameters = array_merge(
                $this->datagridValues,
                $this->getDefaultFilterValues(),
                $filters
            );

            if (!$this->determinedPerPageValue($parameters['_per_page'])) {
                $parameters['_per_page'] = $this->maxPerPage;
            }
        }

        return $parameters;
    }
}
