<?php

namespace swestcott\MonologExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\Definition,
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

        foreach ($config['handlers'] as $name => $config) {

            $handlers[] = $name;

            switch ($config['type']) {
                case 'stream':
                    $container->setDefinition($name, new Definition(
                        'Monolog\Handler\StreamHandler',
                        array(
                            $config['path'],
                            $config['level']
                        )
                    ));
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid or supported handler supplied: "'.$config['type'].'"');
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
                    useAttributeAsKey('name')->
                    prototype('array')->
                        children()->
                            scalarNode('type')->
                                isRequired()->
                                cannotBeEmpty()->
                            end()->
                            scalarNode('level')->end()->
                            scalarNode('path')->end()-> // stream
                            // add
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