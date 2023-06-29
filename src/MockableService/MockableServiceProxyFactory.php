<?php

namespace Tweakers\Test\MockableService;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;

/**
 * Factory for a MockableService proxy derived from a 'original' service.
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class MockableServiceProxyFactory extends AccessInterceptorValueHolderFactory
{
    /**
     * Create a service-proxy based on the given original.
     *
     * @param object $original The service to proxy and use as original.
     *
     * @return MockableService
     */
    public function createServiceProxy(object $original): MockableService
    {
        /** @var MockableService $proxy */
        $proxy = $this->createProxy($original);

        $proxy->setOriginalService($original);

        return $proxy;
    }

    /**
     * Similar to createServiceProxy, but with the class/interface to proxy explicitly specified.
     *
     * This allows proxying of instances of final implementations of the given interface.
     *
     * @param object $original The service to use as original.
     * @param string $className The class (interface) to proxy.
     *
     * @return MockableService
     */
    public function createInterfaceServiceProxy(object $original, string $className): MockableService
    {
        // Body copied from parent::createProxy

        /** @var MockableService $proxy */
        $proxyClassName = $this->generateProxy($className);

        /**
         * We ignore type checks here, since `staticProxyConstructor` is not interfaced (by design)
         *
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress MixedReturnStatement
         */
        $proxy = $proxyClassName::staticProxyConstructor($original, [], []);

        $proxy->setOriginalService($original);

        return $proxy;
    }

    /** {@inheritdoc} */
    protected function getGenerator(): ProxyGeneratorInterface
    {
        return new MockableServiceProxyGenerator();
    }
}
