<?php

namespace swestcott\MonologExtension\Context\Initializer;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;

class MonologInitializerTest extends \PHPUnit_Framework_TestCase
{
    private $loggerName = 'unitTest';

    private function getContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('behat.monolog.logger_name', $this->loggerName);
        $container->setParameter('behat.monolog.class', 'myTest');
        return $container;
    }

    /**
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::__construct
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::initialize
     */
    public function testInitialize()
    {
        $container = $this->getContainer();

        // Create mock logger and register with DIC
        $mockLogger = $this->getMock('\Monolog\Logger', null, array(), 'mockLogger', false);
        $container->setDefinition('behat.monolog.logger.manager', new Definition('mockLogger'));

        $initilizer = new MonologInitializer($container);
        $context = $this->getMock('Behat\Behat\Context\SubcontextableContextInterface');

        $this->assertObjectNotHasAttribute($this->loggerName, $context);

        $initilizer->initialize($context);

        $this->assertObjectHasAttribute($this->loggerName, $context);
        $this->assertInstanceOf('mockLogger', $context->{$this->loggerName});
    }

    /**
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::__construct
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::supports
     */
    public function testInitializerWithSupportedContext()
    {
        $container = $this->getContainer();
        $container->compile();
        $initializer = new MonologInitializer($container);

        $context = $this->getMock('Behat\Behat\Context\SubcontextableContextInterface');

        $this->assertTrue($initializer->supports($context));
    }

    /**
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::__construct
     * @covers swestcott\MonologExtension\Context\Initializer\MonologInitializer::supports
     */
    public function testInitializerWithUnsupportedContext()
    {
        $container = $this->getContainer();
        $container->compile();
        $initializer = new MonologInitializer($container);

        $context = $this->getMock('Behat\Behat\Context\ContextInterface');

        $this->assertFalse($initializer->supports($context));
    }
}
