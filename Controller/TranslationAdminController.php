<?php

namespace WeProvide\TranslationBundle\Controller;

use JMS\TranslationBundle\Translation\Dumper\YamlDumper;
use JMS\TranslationBundle\Translation\FileWriter;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TranslationAdminController extends CRUDController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $catalogue = $this->getCatalogue();

        return $this->render('WeProvideTranslationBundle:TranslationAdmin:list.html.twig', array(
            'catalogue' => $catalogue,
//            'action' => 'list'
        ));
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     *
     * @return RedirectResponse
     */
    public function extractAction()
    {
        // Extracting the translations is done using the extract command of the JMSTranslationBundle. Help on the syntax of that bundle can be
        // obtained by `php bin/console translation:extract --help`. For more information see https://jmsyst.com/bundles/JMSTranslationBundle/master/usage

        // translation:extract nl_NL --bundle=AppBundle --output-dir=./app/Resources/translations --exclude-dir=@WeProvideTranslationBundle


        $resourcePath     = $this->getParameter('we_provide_translation.resource');
        $supportedLocales = $this->getParameter('we_provide_translation.locales');
        $translateBundles = $this->getParameter('we_provide_translation.translate_bundles');
        $fileLocator      = $this->get('file_locator');

//        echo "<pre>";
//        var_dump($translateBundles);
//        var_dump($supportedLocales);
//        die();

        // Default parameters.
        $parameters = array(
            'command'         => 'translation:extract',
            'locales'         => $supportedLocales,
            '--output-format' => 'yml',
            '--output-dir'    => $fileLocator->locate($resourcePath),
            '--exclude-dir'   => [$fileLocator->locate('@WeProvideTranslationBundle')],
            '--keep',
        );
        if ($translateBundles) {
            foreach ($translateBundles as $translateBundle) {
                $parameters["--bundle"] = $translateBundle;
                $this->extractCommand($parameters);
            }
        } else {
            $this->extractCommand($parameters);
        }

        // TODO: translate message in our domain
        $this->addFlash('sonata_flash_success', 'Transalations extracted');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    private function extractCommand($parameters)
    {
        $application = new Application($this->get('kernel'));
        $application->setAutoExit(false);

        $command    = $application->find('translation:extract');
        $input      = new ArrayInput($parameters);
        $output     = new BufferedOutput();
        $returnCode = $command->run($input, $output);
        $content    = $output->fetch();
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
