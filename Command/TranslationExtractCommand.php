<?php

namespace WeProvide\TranslationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use WeProvide\TranslationBundle\Entity\TranslationMessage;
use WeProvide\TranslationBundle\Entity\TranslationMessageLocale;
use WeProvide\TranslationBundle\Repository\TranslationMessageLocaleRepository;
use WeProvide\TranslationBundle\Repository\TranslationMessageRepository;
use WeProvide\TranslationBundle\Repository\TranslationRepository;

class TranslationExtractCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        // Set the name of this command so we can call it from the command line by: php bin/console weprovide:translation:extract
        $this->setName("weprovide:translation:extract");
        $this->setDescription("Extracts translation messages from your code and converts the catalog into ORM.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->extractAction();
        $output->writeln('Transalations succesfully extracted');
    }
    
    protected function extractAction()
    {
        // Extracting the translations is done using the extract command of the JMSTranslationBundle. Help on the syntax of that bundle can be
        // obtained by `php bin/console translation:extract --help`. For more information see https://jmsyst.com/bundles/JMSTranslationBundle/master/usage
        // Or examine vendor/jms/translation-bundle/JMS/TranslationBundle/Command/ExtractTranslationCommand.php

        $resourcePath     = $this->getContainer()->getParameter('we_provide_translation.resource');
        $supportedLocales = $this->getContainer()->getParameter('we_provide_translation.locales');
        $translateBundles = $this->getContainer()->getParameter('we_provide_translation.translate_bundles');
        $fileLocator      = $this->getContainer()->get('file_locator');

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
    }

    /**
     * Executes the command of JMSTranslationBundle to extract the translations.
     *
     * @param $parameters
     * @throws \Exception
     */
    protected function extractCommand($parameters)
    {
        $application = new Application($this->getContainer()->get('kernel'));
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
        $translationRepository = $this->getContainer()->get('we_provide_translation_repository');
        /** @var TranslationMessageRepository $messageRepository */
        $messageRepository = $this->getContainer()->get('doctrine')->getRepository(TranslationMessage::class);
        /** @var TranslationMessageLocaleRepository $messageLocaleRepository */
        $messageLocaleRepository = $this->getContainer()->get('doctrine')->getRepository(TranslationMessageLocale::class);

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