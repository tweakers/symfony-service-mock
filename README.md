# Mockable Service proxy generator

## Introduction
This library is designed to be used with Symfony's service-container 
and leverages [Ocramius' Proxy Manager](https://packagist.org/packages/ocramius/proxy-manager) to allow you to configure an 'original' implementation
of a service that can be changed to an 'alternative' implementation on-the-fly.

As of Symfony 4.0 you're not allowed to change or remove a service once it has been initialized. 
As such, when you need a temporary test double in a unit or functional test, you cannot simply replace the service.

This library allows you to add an alternative configuration to Symfony, which replaces the service with a special proxy that will allow you to
gain complete control over the 'internals', even after Symfony has initialized the service and started using it as dependency in related services.

## Installation

Just include this as a dev dependency:
```
composer require --dev tweakers/symfony-service-mock
```

## How to use
Any service that is configured in Symfony can be re-configured in the [test-environment's](https://symfony.com/doc/current/testing.html) specific services.yaml.
For this library to work optimally, you should reconfigure those services as a [decorator](https://symfony.com/doc/current/service_container/service_decoration.html).

If you have a service 'App\TestService' in your regular configuration, you can configure it like this to allow mocking its internal behavior:
```yaml
# In config\packages\test\services.yaml:
services:
  _defaults:
    public: true
    autowire: true

  Tweakers\Test\MockableService\MockableServiceProxyFactory: ~

  App\TestService_mocked:
    decorates: App\TestService
    decoration_inner_name: 'App\TestService.inner'
    factory: ['@Tweakers\Test\MockableService\MockableServiceProxyFactory', 'createServiceProxy']
    arguments: ['@App\TestService.inner']
```

With just this test-configuration, there is normally no difference in behavior. Although there may be minor changes due to the use of a proxy (see [Ocramius' manual](https://ocramius.github.io/ProxyManager/docs/lazy-loading-value-holder.html))
or the fact that it was made public.

But all services that depend on the _App\TestService_ will now use the newly configured decorating proxy. Which by default falls back to the original service for any actual work.

Therefor this configuration allows you to adjust the behavior of the _TestService_ without having to know which service is using it or whether Symfony already initiated it. That can be done in a unit test like so:

```php
<?php

namespace App;

class TestService
{
    private $stuff;

    public function getStuff()
    {
        return $this->stuff;
    }

    public function setStuff($stuff)
    {
        $this->stuff = $stuff;
    }
}
```

```php
<?php

namespace App\Tests;

use App\TestService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestServiceTest extends KernelTestCase
{
    protected function setUp()
    {
    	parent::setUp();

    	self::bootKernel();
    }

    protected function tearDown()
    {
        parent::tearDown();
        
        // Make sure the original version is restored inside the proxy
        self::$container->get(TestService::class)->restoreOriginalService();
    }

    public function testServiceBehaviorCanBeChanged(): void
    {
        // Set the value of "stuff" to 39 in the original TestService
        $service = self::$container->get(TestService::class);
        $service->setStuff(39);

        $this->assertSame(39, $service->getStuff());

        // Set our mock as alternative for this test
        $mock = $this->createMock(TestService::class);
        $mock->method('getStuff')->willReturn(42);
        
        $service->setAlternativeService($mock);

        $this->assertSame(42, $service->getStuff());
        
        // Revert to original service
        $service->restoreOriginalService();
        $this->assertSame(39, $service->getStuff());
    }
}
``` 

## Compatibility
This code has only been tested with Symfony 3.4, 4.1 and 4.2, although it should work with 4.0 and older versions.
In Symfony 4.1 a [TestContainer](https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing) was introduced that gives access to private services. So it may not be necessary to define a service as public with Symfony 4.1 and higher.
Symfony however does 'inline' or completely remove unused private services (even in 4.1), so tests may fail due to missing services if you define everything private.
To prevent inlining, the example above creates the proxies with public=true, but it may also be necessary to redefine specific services as well.
