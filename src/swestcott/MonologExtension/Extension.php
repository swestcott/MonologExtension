<?php

namespace swestcott\MonologExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Extension\ExtensionInterface;

class Extension implements ExtensionInterface
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.DIRECTORY_SEPARATOR.'services'));
        $loader->load('core.xml');

        if (isset($config['logger_name'])) {
            $container->setParameter('behat.monolog.logger_name', $config['logger_name']);
        }

        $handlers = array();

        foreach ($config['handlers'] as $name => $value) {
            $handlers[] = $name;
            $handlerNs = 'behat.monolog.handlers.'.$name.'.';
            $container->setParameter($handlerNs.'type', $value['type']);

            if (isset($value['path'])) {
                $container->setParameter($handlerNs.'path', $value['path']);
            }

            if (isset($value['level'])) {
                $container->setParameter($handlerNs.'level', $value['level']);
            }
        }

        $container->setParameter('behat.monolog.handlers', $handlers);
    }

    public function getConfig(ArrayNodeDefinition $builder) {
        $builder->
            children()->
                scalarNode('logger_name')->
                    defaultValue('logger')->
                end()->
                arrayNode('handlers')->
                    isRequired()->
                    requiresAtLeastOneElement()->
//                    useAttributeAsKey('handler_name')->
                    prototype('array')->
                        children()->
                            scalarNode('type')->
                                isRequired()->
                                cannotBeEmpty()->
                            end()->
                            scalarNode('path')->end()->
                            scalarNode('level')->end()->
                        end()->
                    end()->
                end()->
            end()->
        end();
    }

    public function getCompilerPasses() {
        return array();
    }
}