<?php

namespace WeProvide\TranslationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use WeProvide\TranslationBundle\Entity\TranslationMessage;
use WeProvide\TranslationBundle\Repository\TranslationRepository;

class TranslationMessageAdmin extends AbstractAdmin
{
    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('extract');

        $collection->remove('create');
        $collection->remove('delete');
    }

    /**
     * @param      $action
     * @param null $object
     * @return array|void
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        // TODO: only add extract action when user is allowed to?
        $list['extract'] = [
            'template' => 'WeProvideTranslationBundle:translation:action/extract.html.twig',
        ];

        return $list;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('domain', null, ['global_search' => true])
            ->add('label', null, ['global_search' => true]);

        $datagridMapper
            ->add('locales.value', null, ['global_search' => true]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->add('id')
            ->add('domain')
            ->add('label')
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                ),
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('domain', null, ['attr' => ['readonly' => true]])
            ->add('label', null, ['attr' => ['readonly' => true]]);

        $formMapper
            ->add('locales', 'sonata_type_collection', [
                'label'        => false,
                'by_reference' => false,
                'type_options' => [
                    'delete' => false,
                ],
            ], [
                'edit'   => 'inline',
                'inline' => 'table',
            ]);
    }

//    public function preUpdate($object)
//    {
//        //todo: set object has changed flag?
//    }

    /**
     * Stores the values into the translation yaml files.
     *
     * @param TranslationMessage $object
     */
    public function postUpdate($object)
    {
        $locales = [];
        foreach ($object->getLocales() as $locale) {
            $locales[$locale->getLocale()] = $locale->getValue();
        }

        $container = $this->getConfigurationPool()->getContainer();

        /** @var TranslationRepository $translationRepository */
        $translationRepository = $container->get('we_provide_translation_repository');

        $translationRepository->updateTranslation(
            $object->getDomain(),
            $object->getLabel(),
            $locales
        );
    }

    /**
     * @inheritDoc
     */
    public function toString($object)
    {
        return '[' . $object->getDomain() . "] > " . $object->getLabel();
    }
}
