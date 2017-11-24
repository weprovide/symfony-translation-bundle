<?php

namespace WeProvide\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use WeProvide\TranslationBundle\Filter\BaseFilter;

class TranslationAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'we_provide_admin_translation';
    protected $baseRoutePattern = 'we_provide_admin_translation';


    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('extract');
        $collection->remove('create');
// TODO: also support create & delete (for translations we can't extract, for instance because concatenated labels are used, or labels based on variables)
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


    public function getFilters()
    {

        $filters = array(
            new BaseFilter(
                'Domain',
                array(
                    'label' => 'Domain',
                )
            ),
            new BaseFilter(
                'Id',
                array(
                    'label' => 'Label',
                )
            ),
        );

        return $filters;
    }


    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @param int  $nbLinks The maximum number of page numbers to return
     * @param      $numberOfPages
     * @return array
     */
    public function getLinks($nbLinks = null, $numberOfPages)
    {
        if ($nbLinks == null) {
            $nbLinks = $this->getMaxPageLinks();
        }

        $filter = $this->getFilterParameters();
        $page   = $filter['_page'];

        $links = array();
        $tmp   = $page - floor($nbLinks / 2);
        $check = $numberOfPages - $nbLinks + 1;
        $limit = $check > 0 ? $check : 1;
        $begin = $tmp > 0 ? ($tmp > $limit ? $limit : $tmp) : 1;

        $i = (int)$begin;
        while ($i < $begin + $nbLinks && $i <= $numberOfPages) {
            $links[] = $i++;
        }

        return $links;
    }

    /**
     * Returns true if the current query requires pagination.
     *
     * @param $numberOfResults
     * @return bool
     */
    public function haveToPaginate($numberOfResults)
    {
        return $this->getMaxPerPage() && $numberOfResults > $this->getMaxPerPage();
    }
}
