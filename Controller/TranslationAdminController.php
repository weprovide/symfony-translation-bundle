<?php

namespace WeProvide\TranslationBundle\Controller;

use JMS\TranslationBundle\Translation\Dumper\YamlDumper;
use JMS\TranslationBundle\Translation\FileWriter;
use Sonata\AdminBundle\Controller\CRUDController;

class TranslationAdminController extends CRUDController
{
    public function listAction()
    {
        $catalogue = $this->getCatalogue();

        return $this->render('WeProvideTranslationBundle:TranslationAdmin:list.html.twig', array(
            'catalogue' => $catalogue,
        ));
    }

    public function editAction($id = null)
    {
        $domain = $this->getRequest()->get('domain');

        if ($this->getRequest()->isMethod('POST')) {
            $localeString = $this->getRequest()->get('localeString');
            $this->updateTranslation($domain, $id, $localeString);

            // TODO: add flash message "updated" or something....
            // TODO: if action is "submit & close" redirect to list.
        }


        $translations = $this->getTranslations($domain, $id);

        return $this->render('WeProvideTranslationBundle:TranslationAdmin:edit.html.twig', array(
            'domain'       => $domain,
            'id'           => $id,
            'translations' => $translations,
        ));
    }


    // TODO: move functions below to TranslationRepository

    public function getCatalogue($locale = null)
    {
        $defaultLocale = $this->getParameter('we_provide_translation.default_locale');
        $resourcePath  = $this->getParameter('we_provide_translation.resource');
        $fileLocator   = $this->get('file_locator');
        $resourcePath  = $fileLocator->locate($resourcePath); // TODO: might throw error if path does not exist or is empty, maybe we should mkdir the path?
        $jmsLoader     = $this->get("jms_translation.loader_manager");
        $catalogue     = $jmsLoader->loadFromDirectory($resourcePath, ($locale ? $locale : $defaultLocale));

        return $catalogue;
    }

    public function getTranslations($domain, $id)
    {
        $translations     = array();
        $supportedLocales = $this->getParameter('we_provide_translation.locales');
        foreach ($supportedLocales as $supportedLocale) {
            $catalogue                      = $this->getCatalogue($supportedLocale);
            $collection                     = $catalogue->getDomain($domain);
            $translation                    = $collection->get($id);
            $translations[$supportedLocale] = $translation;
        }

        return $translations;
    }

    public function updateTranslation($domain, $id, $localeString)
    {
        $resourcePath = $this->getParameter('we_provide_translation.resource');
        $fileLocator  = $this->get('file_locator');
        $resourcePath = $fileLocator->locate($resourcePath);
        $yamlDumper   = new YamlDumper();
        $fileWriter   = new FileWriter(array("yml" => $yamlDumper));
        foreach ($localeString as $locale => $trans) {
            $file      = $resourcePath."/".$domain.".".$locale.".yml";
            $catalogue = $this->getCatalogue($locale);
            $message   = $catalogue->get($id, $domain);
            $message->setLocaleString($trans);
            $catalogue->set($message);
            $fileWriter->write($catalogue, $domain, $file, "yml");
        }
    }
}
