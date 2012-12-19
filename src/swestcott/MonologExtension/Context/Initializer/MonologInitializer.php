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
        $class = $this->container->getParameter('behat.monolog.class');
        
        $def = $this->container->getDefinition('behat.monolog.logger.manager');
        $def->setArguments(array(get_class($context)));
        
        // In theory, this uses my factory to generate the logger instance, 
        // passing in the Behat Context class name to factory->get($name)
        $logger = $this->container->get('behat.monolog.logger.manager');

        // Finally, attached logger to (sub-)context
        $context->$loggerName = $logger;
    }

    public function supports(ContextInterface $context)
    {
        return ($context instanceof SubcontextableContextInterface);
    }
}
