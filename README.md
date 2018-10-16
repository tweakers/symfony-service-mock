# Mockable Service proxy generator

## Introduction
This library is designed to be used with Symfony's service-container 
and leverages [Ocramius' Proxy Manager](https://packagist.org/packages/ocramius/proxy-manager) to offer an 'original' implementation
of a service that can be changed to 'alternative' implementation on-the-fly.

It is created to work seamlessly with Symfony's much stricter container in version 4.0. 
As of the 4.0 version you're not allowed to change or remove a service once it has been used once, you effectively cannot replace it with a
mock-version of the same service. While this may be a bit of a code smell, 
its also a very convenient way to replace some functionality on-demand in (integration/functional) tests, especially when you'd otherwise 
have to manually set up a whole hierarchy of services just to mock one.

## How to use
The intended behavior for this library is to configure your services as always, but reconfigure services you want to be _able_ to mock
in your test-configuration as a [decorator](https://symfony.com/doc/current/service_container/service_decoration.html) with this proxy generator.


If you have a service 'MyNameSpace\MyTestService' in you want to occasionally mock/fake:
```yaml
# In production's config.yaml:
services:
    MyNameSpace\MyTestService: ~
```

```yaml
# In test's config.yaml:
services:
    _defaults:
        public: true
        autowire: true
        
    # Note; if your service was private, you must make it public for the below code to work.
    # That way, one can be sure its both accessible and will not be inlined.
    # MyNameSpace\MyTestService: ~

    MyNameSpace\MyTestService:
        decorates: MyNameSpace\MyTestService
        decoration_inner_name: 'MyNameSpace\MyTestService.inner'
        factory: ['@Tweakers\Test\Helper\MockableService\MockableServiceProxyFactory', 'createServiceProxy']
        arguments: ['@MyNameSpace\MyTestService.inner']
```

The above tells Symfony to create a decorating service and uses the original service as an argument to the service's (in this case) factory.
With just this test-configuration, there is normally no difference in behavior compared to only the production-config.
Although there may be minor changes due to the use of a proxy or the fact that it was made public.

All your services are now using the proxy-service, but that simply forwards all calls to the original service.

This configuration however does allow you to adjust the behavior of the _MyTestService_ without having to know which service is using it or whether Symfony already initiated it. That can be done in a unit test like so:

```php
<?php

class SomeServiceTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->mockedService = $this->createMock(MyTestService::class);
        
        // Set the temporary replacement; from this moment on the proxy will forward all calls to the mockedService.
        $this->container->get(MyTestService::class)->setAlternative($this->mockedService);
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        
        // Restore the proxy's behavior to use the original (production) service.
        $this->container->get(MyTestService::class)->reset();
    }
    
    /** @test */
    public function do_some_test()
    {
        // Further configure your mock etc
    }
}
``` 

## Compatibility
This code has only been tested with Symfony 3.4. It should work with 4.0 and 4.1 and _may_ work with older versions.
As of version 4.1 a 'TestContainer' is available which should give read access to private services. With that container the need to make these services public may be removed.
