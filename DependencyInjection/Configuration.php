<?php

namespace WeProvide\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('we_provide_translation');

        $rootNode
            ->children()
                ->scalarNode('default_locale')
                    ->defaultValue('en')
                ->end()
                ->arrayNode('locales')
                    ->isRequired()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('resource')
                    ->defaultValue('@WeProvideTranslationBundle/Resources/translations')
//                ->arrayNode('resources')
//                    ->prototype('scalar') ->end()
//                    ->validate()->ifEmpty()->then(function ($v) { return array('name' => $v); })->end()   // TODO: fill array with default path "@WeProvideTranslationBundle/Resources/translations"
                ->end()
                ->arrayNode('translate_bundles')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
