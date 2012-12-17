<?php

namespace swestcott\MonologExtension\Context\Initializer;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface;

class MonologInitializer implements InitializerInterface
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function initialize(ContextInterface $context)
    {
        $loggerName = $this->container->getParameter('behat.monolog.logger_name');
        $class = $this->container->getParameter('behat.monolog.service.class');

        $def = $this->container->getDefinition('behat.monolog.logger');
        $def->addArgument(get_class($context));
        $logger = $this->container->get('behat.monolog.logger');

        $handlers = $this->container->getParameter('behat.monolog.handlers');

        // Register each configured handler with logger
        foreach($handlers as $name) {
            $handler = $this->container->get($name);
            $logger->pushHandler($handler);
        }

        $context->$loggerName = $logger;
    }

    public function supports(ContextInterface $context)
    {
        return get_class($context) === $this->container->getParameter('behat.context.class');
    }
}
