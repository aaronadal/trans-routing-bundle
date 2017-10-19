<?php

namespace Aaronadal\TransRoutingBundle\Router;


use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TransYmlRouteLoader extends YamlFileLoader
{

    protected static $TRANSLATABLE_STRING_CONFIG_VALUES = [
        'path',
        'prefix',
    ];

    protected static $TRANSLATABLE_ARRAY_CONFIG_VALUES = [
        'defaults',
        'requirements',
    ];

    /**
     * Locales for which the routes have to be generated.
     * Other locales out of this array will be ignored.
     *
     * @var array
     */
    private $allowedLocales;

    /**
     * @internal If this variable is not null, only routes for this locale will be imported.
     *
     * @var string|null
     */
    private $_currentImportLocale;

    /**
     * Creates a new CustomRouteLoader instance.
     *
     * @param FileLocatorInterface $locator
     * @param array                $allowedLocales
     */
    public function __construct(FileLocatorInterface $locator, array $allowedLocales)
    {
        parent::__construct($locator);

        $this->allowedLocales       = $allowedLocales;
        $this->_currentImportLocale = null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        $supportsResource  = is_string($resource);
        $supportsExtension = in_array(pathinfo($resource, PATHINFO_EXTENSION), ['yml', 'yaml'], true);
        $supportsType      = 'trans' === $type;

        return $supportsResource && $supportsExtension && $supportsType;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseRoute(RouteCollection $collection, $name, array $config, $path)
    {
        $defaults     = $config['defaults'] ?? [];
        $requirements = $config['requirements'] ?? [];
        $path         = $config['path'];

        foreach($this->getSuitableLocales() as $locale) {
            $config['defaults']     = $this->getLocaleConfigValue($locale, 'defaults', $defaults);
            $config['requirements'] = $this->getLocaleConfigValue($locale, 'requirements', $requirements);
            $config['path']         = $this->getLocaleConfigValue($locale, 'path', $path);

            $transName = "$locale.$name";

            parent::parseRoute($collection, $transName, $config, $path);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function parseImport(RouteCollection $collection, array $config, $path, $file)
    {
        $defaults     = $config['defaults'] ?? [];
        $requirements = $config['requirements'] ?? [];
        $prefix       = $config['prefix'];

        foreach($this->getSuitableLocales() as $locale) {
            $config['defaults']     = $this->getLocaleConfigValue($locale, 'defaults', $defaults);
            $config['requirements'] = $this->getLocaleConfigValue($locale, 'requirements', $requirements);
            $config['prefix']       = $this->getLocaleConfigValue($locale, 'prefix', $prefix);

            $this->_currentImportLocale = $locale;
            parent::parseImport($collection, $config, $path, $file);
            $this->_currentImportLocale = null;
        }
    }

    protected function getSuitableLocales()
    {
        return $this->_currentImportLocale ? [$this->_currentImportLocale] : $this->allowedLocales;
    }

    /**
     * Returns the locale-based value of a config field.
     *
     * @param string       $locale
     * @param string       $configKey
     * @param array|string $configValue
     *
     * @return string
     */
    protected function getLocaleConfigValue($locale, $configKey, $configValue)
    {
        $isStringConfig = in_array($configKey, static::$TRANSLATABLE_STRING_CONFIG_VALUES);
        $isArrayConfig  = in_array($configKey, static::$TRANSLATABLE_ARRAY_CONFIG_VALUES);

        $isStringTransConfig = $isStringConfig && is_array($configValue);
        $isArrayTransConfig  = $isArrayConfig && count($configValue) > 0 && is_array(array_values($configValue)[0]);

        if($isStringTransConfig || $isArrayTransConfig) {
            if(!array_key_exists($locale, $configValue)) {
                throw new \RuntimeException("Missing '$locale' locale in the '$configKey' route config.");
            }

            return $configValue[$locale];
        }

        return $configValue;
    }
}
