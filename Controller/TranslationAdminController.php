<?php

namespace WeProvide\TranslationBundle\Controller;

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
        $filter       = $this->admin->getFilterParameters();
        $repository   = $this->get("we_provide_translation_repository");
        $translations = $repository->findBy(array(), array(), $filter['_per_page'], ($filter['_per_page'] * ($filter['_page'] - 1)));

        return $this->render('WeProvideTranslationBundle:TranslationAdmin:list.html.twig', array(
            'repository'   => $repository,
            'translations' => $translations,
        ));
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null)
    {
        $repository = $this->get("we_provide_translation_repository");
        $domain     = $this->getRequest()->get('domain');

        if ($this->getRequest()->isMethod('POST')) {
            $localeString = $this->getRequest()->get('localeString');
            $repository->updateTranslation($domain, $id, $localeString);

            $this->addFlash(
                'sonata_flash_success',
                $this->get('translator')->trans('Transalation succesfully updated', array(), 'WeProvideTranslationBundle')
            );

            if (null !== $this->getRequest()->get('btn_update_and_list')) {
                $url = $this->admin->generateUrl('list');

                return new RedirectResponse($url);
            }
        }

        $translations = $repository->findOneBy(
            array(
                'domain' => $domain,
                'id'     => $id,
            )
        );

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

        $resourcePath     = $this->getParameter('we_provide_translation.resource');
        $supportedLocales = $this->getParameter('we_provide_translation.locales');
        $translateBundles = $this->getParameter('we_provide_translation.translate_bundles');
        $fileLocator      = $this->get('file_locator');

        // Default parameters.  // TODO: maybe move this to config so other developers can config the command?
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

        $this->addFlash(
            'sonata_flash_success',
            $this->get('translator')->trans('Transalations succesfully extracted', array(), 'WeProvideTranslationBundle')
        );

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * Executes the command of JMSTranslationBundle to extract the translations.
     *
     * @param $parameters
     */
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
}
