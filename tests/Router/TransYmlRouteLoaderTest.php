<?php

namespace Aaronadal\Tests\Router;


use Aaronadal\TransRoutingBundle\Router\TransYmlRouteLoader;
use Symfony\Component\Config\FileLocator;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TransYmlRouteLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TransYmlRouteLoader
     */
    private $loader;

    public function setUp()
    {
        parent::setUp();

        $locator      = new FileLocator(__DIR__ . '/../Fixtures/');
        $this->loader = new TransYmlRouteLoader($locator, ['en', 'es', 'fr']);
    }

    public function testLoadNativeRoutes()
    {
        $collection = $this->loader->load('route-native.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_native'));
        $this->assertEquals($collection->get('en.route_native')->getPath(), '/foo');

        $this->assertNotNull($collection->get('es.route_native'));
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/foo');

        $this->assertNotNull($collection->get('fr.route_native'));
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/foo');
    }

    public function testLoadTransRoutes()
    {
        $collection = $this->loader->load('route-trans.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_trans'));
        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/foo');

        $this->assertNotNull($collection->get('es.route_trans'));
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/bar');

        $this->assertNotNull($collection->get('fr.route_trans'));
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/baz');
    }

    public function testImportRoutes()
    {
        $collection = $this->loader->load('route-import.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_native'));
        $this->assertEquals($collection->get('en.route_native')->getPath(), '/native/foo');

        $this->assertNotNull($collection->get('en.route_trans'));
        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/translated/foo');

        $this->assertNotNull($collection->get('es.route_native'));
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/{_locale}/nativa/foo');

        $this->assertNotNull($collection->get('es.route_trans'));
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/traducida/bar');

        $this->assertNotNull($collection->get('fr.route_native'));
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/{_locale}/natif/foo');

        $this->assertNotNull($collection->get('fr.route_trans'));
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/traduit/baz');
    }
}
