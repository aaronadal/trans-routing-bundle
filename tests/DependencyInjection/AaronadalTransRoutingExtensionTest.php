<?php

namespace Aaronadal\Tests\DependencyInjection;


use Aaronadal\TransRoutingBundle\DependencyInjection\AaronadalTransRoutingExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class AaronadalTransRoutingExtensionTest extends \PHPUnit_Framework_TestCase
{

    const DEFAULT_LOCALE = 'ca';

    /**
     * @var AaronadalTransRoutingExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->extension = new AaronadalTransRoutingExtension();
        $this->container = new ContainerBuilder();

        $this->container->setParameter('kernel.default_locale', self::DEFAULT_LOCALE);
    }

    public function testGetConfigWithDefaultValues()
    {
        $this->extension->load(
            [],
            $container = $this->container
        );

        $this->assertEquals(self::DEFAULT_LOCALE, $container->getParameter('aaronadal.trans_routing.default_locale'));
        $this->assertEquals(
            [self::DEFAULT_LOCALE],
            $container->getParameter('aaronadal.trans_routing.allowed_locales')
        );
    }

    public function testGetConfigWithEmptyDefaultLocale()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->extension->load(
            [
                [
                    'default_locale' => '',
                ],
            ],
            $container = $this->container
        );
    }

    public function testGetConfigWithEmptyAllowedLocales()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->extension->load(
            [
                [
                    'allowed_locales' => ['a', 'b', ''],
                ],
            ],
            $container = $this->container
        );
    }

    public function testGetConfigWithValidValues()
    {
        $this->extension->load(
            [
                [
                    'default_locale' => 'en',
                    'allowed_locales' => ['en', 'es', 'fr', 'ca'],
                ],
            ],
            $container = $this->container
        );

        $this->assertEquals('en', $container->getParameter('aaronadal.trans_routing.default_locale'));
        $this->assertEquals(
            ['en', 'es', 'fr', 'ca'],
            $container->getParameter('aaronadal.trans_routing.allowed_locales')
        );

        $this->extension->load(
            [
                [
                    'allowed_locales' => ['en', 'es', 'fr', 'ca'],
                ],
            ],
            $container = $this->container
        );

        $this->assertEquals(self::DEFAULT_LOCALE, $container->getParameter('aaronadal.trans_routing.default_locale'));
        $this->assertEquals(
            ['en', 'es', 'fr', 'ca'],
            $container->getParameter('aaronadal.trans_routing.allowed_locales')
        );

        $this->extension->load(
            [
                [
                    'default_locale' => 'en',
                ],
            ],
            $container = $this->container
        );

        $this->assertEquals('en', $container->getParameter('aaronadal.trans_routing.default_locale'));
        $this->assertEquals(
            ['en'],
            $container->getParameter('aaronadal.trans_routing.allowed_locales')
        );
    }
}
