<?php

namespace swestcott\MonologExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class Factory
{
    public function get(ContainerBuilder $container, $name) {
        $logger = new \Monolog\Logger($name);

        // Grab *all* handlers and attached to logger
        // TODO - grab only those hanlders required by the logger!
        foreach ($container->findTaggedServiceIds('behat.monolog.handler.tag') as $id => $attributes) {
            $handler = $container->get($id);
            $logger->pushHandler($handler);
        }

	return $logger;
    }
}
