<?php

namespace swestcott\MonologExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers swestcott\MonologExtension\Factory::get
     * @expectedException LogicException
     */
    public function testFactory()
    {
        $container = new ContainerBuilder();
        $container->setParameter('behat.monolog.class', '\Monolog\Logger');

        $def = new Definition(
            'Monolog\Handler\StreamHandler',
            array(
                'php://stdout',
                'debug'
            )
        );
        $def->addTag('behat.monolog.handler.tag');
        $container->setDefinition('myHandler', $def);

        $container->compile();

        $factory = new Factory();
        $logger = $factory->get($container, 'myLogger');

        $handler = $logger->popHandler();

        // Not much else available to check...
        $this->assertInstanceOf('\Monolog\Handler\StreamHandler', $handler);
        $this->assertEquals('debug', $handler->getLevel());

        // Shouldn't be anymore handler left, popping another should cause
        // an exception
        $logger->popHandler();
    }
}
