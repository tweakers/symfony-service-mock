<?php

namespace Tweakers\Test\MockableService;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;


/**
 * Factory for a MockableService proxy derived from a 'original' service.
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class MockableServiceProxyFactory extends LazyLoadingValueHolderFactory
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
        $proxy = $this->createProxy(get_class($original), function () use ($original) {return $original; });

        $proxy->setOriginalService($original);

        return $proxy;
    }

    /** {@inheritdoc} */
    protected function getGenerator(): ProxyGeneratorInterface
    {
        return new MockableServiceProxyGenerator();
    }
}
