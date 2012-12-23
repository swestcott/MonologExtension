<?php

namespace swestcott\MonologExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleHandlerAddedToServiceContainer()
    {
        $handlerName = 'myHandler';

        $config = array(
            'handlers' => array(
                $handlerName => array(
                    'type' => 'stream',
                    'path' => 'php://stdout',
                    'level' => 'debug'
                )
            )
        );

        $container = new ContainerBuilder();
        $ext = new Extension();
        $ext->load($config, $container);
        $container->compile();

        $this->assertTrue($container->hasDefinition($handlerName));
        $this->assertEquals(1, count(
            $container->findTaggedServiceIds('behat.monolog.handler.tag')
        ));

        $def = $container->getDefinition($handlerName);
        $this->assertContains('StreamHandler', $def->getClass());
        $args = $def->getArguments();
        $this->assertContains('php://stdout', $args);
        $this->assertContains('debug', $args);
    }
}