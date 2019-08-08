<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CompilerPass to register the dashboard storages
 */
class CmsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if($container->hasDefinition('kalamu.dashboard_registry')){

            $registry = $container->findDefinition('kalamu.dashboard_registry');

            $services = $container->findTaggedServiceIds('dashboard_storage');
            foreach($services as $serviceId => $tags){
                foreach($tags as $tag){
                    $registry->addMethodCall('registerStorage', [$tag['alias'], new Reference($serviceId)]);
                }
            }
        }

    }

}