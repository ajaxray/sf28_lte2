<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Task Runners Pass
 *
 * Register services tagged as "worker.task" to master Worker
 */
class TaskRunnersPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('app.worker.master')) {
            return;
        }

        $masterWorker   = $container->findDefinition('app.worker.master');
        $taggedServices = $container->findTaggedServiceIds('worker.task');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('task', $attributes)) {
                    continue;
                }
                $masterWorker->addMethodCall(
                    'addTask',
                    [$attributes["task"], $id]
                );
            }
        }
    }
}