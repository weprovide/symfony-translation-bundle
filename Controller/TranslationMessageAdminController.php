<?php

namespace WeProvide\TranslationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WeProvide\TranslationBundle\Entity\TranslationMessage;
use WeProvide\TranslationBundle\Entity\TranslationMessageLocale;
use WeProvide\TranslationBundle\Repository\TranslationMessageLocaleRepository;
use WeProvide\TranslationBundle\Repository\TranslationMessageRepository;
use WeProvide\TranslationBundle\Repository\TranslationRepository;

class TranslationMessageAdminController extends CRUDController
{
    /**
     * @return RedirectResponse
     * @throws \Exception
     */
    public function extractAction()
    {
        // Extracting the translations is done using the extract command of the JMSTranslationBundle. Help on the syntax of that bundle can be
        // obtained by `php bin/console translation:extract --help`. For more information see https://jmsyst.com/bundles/JMSTranslationBundle/master/usage
        // Or examine vendor/jms/translation-bundle/JMS/TranslationBundle/Command/ExtractTranslationCommand.php

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
            // TODO: this breaks... so when no bundles to extract is configured the extraction breaks
            $this->extractCommand($parameters);
        }

        // After extraction we need to convert the catalogue into ORM.
        $this->convertCatalogue();

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
     * @throws \Exception
     */
    protected function extractCommand($parameters)
    {
        $application = new Application($this->get('kernel'));
        $application->setAutoExit(false);

        $command    = $application->find('translation:extract');
        $input      = new ArrayInput($parameters);
        $output     = new BufferedOutput();
        $returnCode = $command->run($input, $output);
        $content    = $output->fetch();
    }

    /**
     * Convert the catalogue into ORM.
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function convertCatalogue()
    {
        /** @var TranslationRepository $translationRepository */
        $translationRepository = $this->get('we_provide_translation_repository');
        /** @var TranslationMessageRepository $messageRepository */
        $messageRepository = $this->getDoctrine()->getRepository(TranslationMessage::class);
        /** @var TranslationMessageLocaleRepository $messageLocaleRepository */
        $messageLocaleRepository = $this->getDoctrine()->getRepository(TranslationMessageLocale::class);

        // Truncate translations from database.
        $messageRepository->truncate();
        $messageLocaleRepository->truncate();

        // Get all messages from yaml files.
        $messages = $translationRepository->findAll();

        foreach ($messages as $message) {
            /** @var TranslationMessage $translationMessage */
            $translationMessage = new TranslationMessage();
            $translationMessage->setDomain($message->getDomain());
            $translationMessage->setLabel($message->getId());

            // Store message.
            $translationMessage = $messageRepository->addOne($translationMessage);


            // Get translations from configured locales.
            $translations = $translationRepository->findOneBy([
                'domain' => $message->getDomain(),
                'id'     => $message->getId(),
            ]);

            foreach ($translations as $locale => $translation) {
                $translationMessageLocale = new TranslationMessageLocale();
                $translationMessageLocale->setMessage($translationMessage);
                $translationMessageLocale->setLocale($locale);
                $translationMessageLocale->setValue($translation->getLocaleString());

                // Store message with locale string.
                $messageLocaleRepository->addOne($translationMessageLocale);
            }
        }
    }
}
