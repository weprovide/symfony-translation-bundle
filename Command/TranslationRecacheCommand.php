<?php

namespace WeProvide\TranslationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

class TranslationRecacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // Set the name of this command so we can call it from the command line by: php bin/console translation:recache
        $this->setName("weprovide:translation:recache");
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Kernel $kernel */
        $kernel      = $this->getContainer()->get('kernel');
        $cacheDir    = $kernel->getCacheDir();
        $environment = $kernel->getEnvironment();

        if (@file_exists($cacheDir . '/wpclear')) {
            $output->writeln('Clearing the cache for the <info>' . $environment . '</info> environment with debug <info>false</info>');
            @exec('php bin/console cache:clear --env=' . $environment . ' --no-debug');
        }
    }
}