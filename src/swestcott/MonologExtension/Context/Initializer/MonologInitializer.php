<?php

namespace swestcott\MonologExtension\Context\Initializer;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface;

use Monolog\Logger,
    Monolog\Handler\StreamHandler;

class MonologInitializer implements InitializerInterface
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function initialize(ContextInterface $context)
    {
        $logger = new Logger(get_class($context));

        $handlers = $this->container->getParameter('behat.monolog.handlers');

        // Register each configured handler with logger
        foreach($handlers as $name) {
            $handle = new StreamHandler(
                $this->container->getParameter('behat.monolog.handlers.'.$name.'.path'),
                $this->container->getParameter('behat.monolog.handlers.'.$name.'.level')
            );
            $logger->pushHandler($handle);
        }

        $name = $this->container->getParameter('behat.monolog.logger_name');
        $context->$name = $logger;
    }

    public function supports(ContextInterface $context)
    {
        return get_class($context) === $this->container->getParameter('behat.context.class');
    }
}
