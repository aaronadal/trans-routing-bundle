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

        $locator      = new FileLocator(__DIR__ . '/../Fixtures/routes/');
        $this->loader = new TransYmlRouteLoader($locator, ['en', 'es', 'fr']);
    }

    public function testLoadNativeRoutes()
    {
        $collection = $this->loader->load('path/route-native.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_native'));
        $this->assertEquals($collection->get('en.route_native')->getPath(), '/foo');

        $this->assertNotNull($collection->get('es.route_native'));
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/foo');

        $this->assertNotNull($collection->get('fr.route_native'));
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/foo');
    }

    public function testLoadTransRoutes()
    {
        $collection = $this->loader->load('path/route-trans.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_trans'));
        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/foo');

        $this->assertNotNull($collection->get('es.route_trans'));
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/bar');

        $this->assertNotNull($collection->get('fr.route_trans'));
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/baz');
    }

    public function testImportRoutes()
    {
        $collection = $this->loader->load('path/route-import.yml', 'trans');

        $this->assertNotNull($collection->get('en.route_native'));
        $this->assertNotNull($collection->get('es.route_native'));
        $this->assertNotNull($collection->get('fr.route_native'));

        $this->assertNotNull($collection->get('en.route_trans'));
        $this->assertNotNull($collection->get('es.route_trans'));
        $this->assertNotNull($collection->get('fr.route_trans'));
    }

    public function testRoutesTranslatedPath()
    {
        $collection = $this->loader->load('path/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getPath(), '/native/foo');
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/native/foo');
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/native/foo');

        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/translated/foo');
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/traducida/bar');
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/traduit/baz');
    }

    public function testRoutesMixedPrefix()
    {
        $collection = $this->loader->load('prefix-mixed/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getPath(), '/native/foo');
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/foo');
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/foo');

        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/bar');
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/traducida/bar');
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/bar');
    }

    public function testRoutesTranslatedDefaults()
    {
        $collection = $this->loader->load('defaults/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getDefaults(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('es.route_native')->getDefaults(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('fr.route_native')->getDefaults(), ['one' => 'one', 'two' => 'two']);

        $this->assertEquals($collection->get('en.route_trans')->getDefaults(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('es.route_trans')->getDefaults(), ['one' => 'uno', 'two' => 'dos']);
        $this->assertEquals($collection->get('fr.route_trans')->getDefaults(), ['one' => 'un', 'two' => 'deux']);
    }

    public function testRoutesMixedDefaults()
    {
        $collection = $this->loader->load('defaults-mixed/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getDefaults(), []);
        $this->assertEquals($collection->get('es.route_native')->getDefaults(), ['one' => 'uno']);
        $this->assertEquals($collection->get('fr.route_native')->getDefaults(), ['foo' => 'bar']);

        $this->assertEquals($collection->get('en.route_trans')->getDefaults(), ['bar' => 'baz']);
        $this->assertEquals($collection->get('es.route_trans')->getDefaults(), ['two' => 'dos']);
        $this->assertEquals($collection->get('fr.route_trans')->getDefaults(), []);
    }

    public function testRoutesTranslatedRequirements()
    {
        $collection = $this->loader->load('requirements/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getRequirements(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('es.route_native')->getRequirements(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('fr.route_native')->getRequirements(), ['one' => 'one', 'two' => 'two']);

        $this->assertEquals($collection->get('en.route_trans')->getRequirements(), ['one' => 'one', 'two' => 'two']);
        $this->assertEquals($collection->get('es.route_trans')->getRequirements(), ['one' => 'uno', 'two' => 'dos']);
        $this->assertEquals($collection->get('fr.route_trans')->getRequirements(), ['one' => 'un', 'two' => 'deux']);
    }

    public function testRoutesMixedRequirements()
    {
        $collection = $this->loader->load('requirements-mixed/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getRequirements(), []);
        $this->assertEquals($collection->get('es.route_native')->getRequirements(), ['one' => 'uno']);
        $this->assertEquals($collection->get('fr.route_native')->getRequirements(), ['foo' => 'bar']);

        $this->assertEquals($collection->get('en.route_trans')->getRequirements(), ['bar' => 'baz']);
        $this->assertEquals($collection->get('es.route_trans')->getRequirements(), ['two' => 'dos']);
        $this->assertEquals($collection->get('fr.route_trans')->getRequirements(), []);
    }

    public function testRoutesOneImport()
    {
        $collection = $this->loader->load('one-import/route-import.yml', 'trans');

        $this->assertEquals($collection->get('en.route_native')->getPath(), '/import/native/foo');
        $this->assertEquals($collection->get('es.route_native')->getPath(), '/importar/native/foo');
        $this->assertEquals($collection->get('fr.route_native')->getPath(), '/importer/native/foo');
        $this->assertEquals($collection->get('en.route_native')->getRequirements(), []);
        $this->assertEquals($collection->get('es.route_native')->getRequirements(), ['one' => 'uno']);
        $this->assertEquals($collection->get('fr.route_native')->getRequirements(), ['three' => 'trois', 'foo' => 'bar']);

        $this->assertEquals($collection->get('en.route_trans')->getPath(), '/import/translated/bar');
        $this->assertEquals($collection->get('es.route_trans')->getPath(), '/importar/translated/bar');
        $this->assertEquals($collection->get('fr.route_trans')->getPath(), '/importer/translated/bar');
        $this->assertEquals($collection->get('en.route_trans')->getRequirements(), ['bar' => 'baz']);
        $this->assertEquals($collection->get('es.route_trans')->getRequirements(), ['two' => 'dos']);
        $this->assertEquals($collection->get('fr.route_trans')->getRequirements(), ['three' => 'trois']);
    }
}
