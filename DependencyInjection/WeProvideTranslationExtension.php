<?php

namespace WeProvide\TranslationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WeProvideTranslationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        // Set all of our configs in an array so we can inject it into a service.
        $container->setParameter('we_provide_translation.config', $config);

        // Set all configs as single value.
        $container->setParameter('we_provide_translation.default_locale', $config['default_locale']);
        $container->setParameter('we_provide_translation.locales', $config['locales']);
        $container->setParameter('we_provide_translation.resource', $config['resource']);
        $container->setParameter('we_provide_translation.translate_bundles', $config['translate_bundles']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
