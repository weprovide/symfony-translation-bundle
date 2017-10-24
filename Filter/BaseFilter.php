<?php

namespace WeProvide\TranslationBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Filter\Filter;

class BaseFilter extends Filter
{
    /**
     * SomeFilter constructor.
     * @param $name
     * @param $options
     */
    public function __construct($name, $options)
    {
        $this->initialize($name, $options);
    }

    /**
     * Apply the filter to the QueryBuilder instance.
     *
     * @param ProxyQueryInterface $queryBuilder
     * @param string              $alias
     * @param string              $field
     * @param string              $value
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $value)
    {
        // TODO: Implement filter() method.
    }

    /**
     * @param mixed $query
     * @param mixed $value
     */
    public function apply($query, $value)
    {
        // TODO: Implement apply() method.
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            'translation_domain' => 'WeProvideTranslationBundle',
        );
    }

    /**
     * Returns the main widget used to render the filter.
     *
     * @return array
     */
    public function getRenderSettings()
    {
        // TODO: Implement getRenderSettings() method.
    }
}