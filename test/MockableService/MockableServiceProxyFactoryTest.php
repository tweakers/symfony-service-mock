<?php

namespace Tweakers\Test\MockableService;

use PHPUnit\Framework\TestCase;

/**
 * Note: this is more of an integration test rather than a unit test.
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class MockableServiceProxyFactoryTest extends TestCase
{
    /** @var FakeService */
    private $original;
    /** @var FakeService */
    private $alternative;

    protected function setUp(): void
    {
        parent::setUp();

        $this->original    = new FakeService('orig');
        $this->alternative = new FakeService('mock');
    }

    /** @test */
    public function it_should_generate_a_mockable_service_proxy(): void
    {
        $factory = new MockableServiceProxyFactory();
        /** @var FakeService&MockableService $proxy */
        $proxy = $factory->createServiceProxy($this->original);

        $this->assertInstanceOf(MockableService::class, $proxy);
        $this->assertInstanceOf(FakeService::class, $proxy);

        $this->assertEquals($this->original->getValue(), $proxy->getValue());
    }

    /** @test */
    public function it_should_generate_a_working_set_alternative(): void
    {
        $factory = new MockableServiceProxyFactory();
        /** @var FakeService&MockableService $proxy */
        $proxy = $factory->createServiceProxy($this->original);

        $proxy->setAlternativeService($this->alternative);
        $this->assertEquals($this->alternative->getValue(), $proxy->getValue());
    }

    /** @test */
    public function it_should_generate_a_working_reset(): void
    {
        $factory = new MockableServiceProxyFactory();
        /** @var FakeService&MockableService $proxy */
        $proxy = $factory->createServiceProxy($this->original);

        $proxy->setAlternativeService($this->alternative);
        $proxy->restoreOriginalService();

        $this->assertEquals($this->original->getValue(), $proxy->getValue());
    }

    /** @test */
    public function it_should_generate_a_mockable_service_proxy_via_an_interface(): void
    {
        $original = new FinalFakeService('value');

        $factory = new MockableServiceProxyFactory();
        /** @var FinalFakeService&MockableService $proxy */
        $proxy = $factory->createInterfaceServiceProxy($original, TestInterface::class);

        $this->assertInstanceOf(MockableService::class, $proxy);
        $this->assertInstanceOf(TestInterface::class, $proxy);

        $this->assertEquals($original->getValue(), $proxy->getValue());
    }
}

interface TestInterface
{
    public function getValue(): string;
}

class FakeService implements TestInterface
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

final class FinalFakeService implements TestInterface
{
    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
