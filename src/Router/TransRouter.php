<?php


namespace Aaronadal\TransRoutingBundle\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;


/**
 * @author Aarón Nadal <aaronadal.dev@gmail.com>
 */
class TransRouter implements RouterInterface, RequestMatcherInterface, WarmableInterface
{

    /**
     * @var RouterInterface|RequestMatcherInterface|WarmableInterface
     */
    private $router;
    private $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router       = $router;
        $this->requestStack = $requestStack;
    }

    private function getLocale()
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request->getLocale();
    }

    /**
     * {@inheritdoc}
     *
     * Tries to generate a translated route from the route name provided.
     *
     * - If the _locale.name route exists, returns it.
     * - If not, returns the name route.
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $currentLocale = $this->getLocale();
        $locale        = $parameters['_locale'] ?? $currentLocale;

        if (substr($name, 0, strlen($currentLocale) + 1) === $currentLocale . '.') {
            $name = $locale . '.' . substr($name, strlen($currentLocale) + 1);
        }
        else if ($this->getRouteCollection()->get($locale . '.' . $name) !== null) {
            $name = $locale . '.' . $name;
        }

        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function getRouteCollection()
    {
        return $this->router->getRouteCollection();
    }

    public function setContext(RequestContext $context)
    {
        $this->router->setContext($context);
    }

    public function match($pathinfo)
    {
        return $this->router->match($pathinfo);
    }

    public function getContext()
    {
        return $this->router->getContext();
    }

    public function matchRequest(Request $request)
    {
        return $this->router->matchRequest($request);
    }

    public function warmUp($cacheDir)
    {
        $this->router->warmUp($cacheDir);
    }
}
