<?php
namespace Mopa\Bundle\BootstrapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class NavbarPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('mopa_bootstrap.navbar_renderer')) {
            return;
        }
        $definition = $container->getDefinition('mopa_bootstrap.navbar_renderer');

        $template = $container->getParameter('mopa_bootstrap.navbar.template');
        if(strpos($template, "%d") !== false){
            $version = $container->getParameter('mopa_bootstrap.version');
            $template = sprintf($template, $version); 
            $container->setParameter('mopa_bootstrap.navbar.template', $template);
        }
        
        $navbars = array();
        foreach ($container->findTaggedServiceIds('mopa_bootstrap.navbar') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "mopa_bootstrap.navbar" tag for the service "%s"', $id));
                }
                $navbars[$attributes['alias']] = $id;
            }
        }
        $definition->replaceArgument(1, $navbars);
    }
}
