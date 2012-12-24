<?php

namespace swestcott\MonologExtension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers swestcott\MonologExtension\Extension::load
     */
    public function testSetLoggerName()
    {
        $loggerName = 'myLogger';
        $config = array(
            'logger_name' => $loggerName,
            'handlers' => array()
        );

        $container = new ContainerBuilder();
        $ext = new Extension();
        $ext->load($config, $container);
        $container->compile();

        $this->assertTrue($container->hasParameter('behat.monolog.logger_name'));
        $this->assertEquals($loggerName, $container->getParameter('behat.monolog.logger_name'));
    }

    /**
     * @covers swestcott\MonologExtension\Extension::load
     */
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

    /**
     * @covers swestcott\MonologExtension\Extension::load
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownHandler()
    {
        $config = array(
            'handlers' => array(
                'null' => array(
                    'type' => 'doesnotexist'
                )
            )
        );

        $container = new ContainerBuilder();
        $ext = new Extension();
        $ext->load($config, $container);
    }

    public function testTreeBuilderSuccess()
    {
        $config = array(
            'behat' => array(
                'logger_name' => 'myLogger',
                'handlers' => array(
                    'myHandler' => array(
                        'type' => 'stream'
                    )
                )
            )
        );

        $builder = new TreeBuilder();
        $node = $builder->root('behat', 'array');

        $ext = new Extension();
        $ext->getConfig($node);

        $tree = $builder->buildTree();

        $processor = new Processor();
        $processedConfig = $processor->process($tree, $config);

        $this->assertEquals($config['behat'], $processedConfig);
    }

    /**
     * @covers swestcott\MonologExtension\Extension::getConfig
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testTreeBuilderNoHandlersException()
    {
        $config = array(
            'behat' => array(
                'handlers' => array()
            )
        );

        $builder = new TreeBuilder();
        $node = $builder->root('behat', 'array');

        $ext = new Extension();
        $ext->getConfig($node);

        $tree = $builder->buildTree();

        $processor = new Processor();
        $processor->process($tree, $config);
    }

    /**
     * @covers swestcott\MonologExtension\Extension::getCompilerPasses
     */
    public function testCompilerPassed()
    {
        $ext = new Extension();
        $this->assertEmpty($ext->getCompilerPasses());
    }
}