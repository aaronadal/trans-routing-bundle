<?php

namespace Aaronadal\TransRoutingBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class AaronadalTransRoutingExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        if($config['default_locale'] === null) {
            $container->setParameter(
                'aaronadal.trans_routing.default_locale',
                $container->getParameter('kernel.default_locale')
            );
        }
        else {
            $container->setParameter(
                'aaronadal.trans_routing.default_locale',
                $config['default_locale']
            );
        }

        if(count($config['allowed_locales']) === 0) {
            $container->setParameter(
                'aaronadal.trans_routing.allowed_locales',
                [$container->getParameter('aaronadal.trans_routing.default_locale')]
            );
        }
        else {
            $container->setParameter(
                'aaronadal.trans_routing.allowed_locales',
                $config['allowed_locales']
            );
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
