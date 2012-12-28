<?php

namespace swestcott\MonologExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Factory
{
    // TODO - refactor out container dep
    public function get(ContainerBuilder $container, $name) {
        $class = $container->getParameter('behat.monolog.class');
        $logger = new $class($name);

        // Grab *all* handlers and attached to logger
        // TODO - grab only those hanlders required by the logger!
        foreach ($container->findTaggedServiceIds('behat.monolog.handler.tag') as $id => $attributes) {
            $handler = $container->get($id);
            $logger->pushHandler($handler);
        }

        return $logger;
    }
}
