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

    /** @var MockableService|FakeService $proxy */
    private $proxy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->original    = new FakeService('orig');
        $this->alternative = new FakeService('mock');

        $factory = new MockableServiceProxyFactory();

        $this->proxy = $factory->createServiceProxy($this->original);
    }

    /** @test */
    public function it_should_generate_a_mockable_service_proxy(): void
    {
        $this->assertInstanceOf(MockableService::class, $this->proxy);
        $this->assertInstanceOf(FakeService::class, $this->proxy);

        $this->assertEquals($this->original->getValue(), $this->proxy->getValue());
    }

    /** @test */
    public function it_should_generate_a_working_set_alternative(): void
    {
        $this->proxy->setAlternativeService($this->alternative);
        $this->assertEquals($this->alternative->getValue(), $this->proxy->getValue());
    }

    /** @test */
    public function it_should_generate_a_working_reset(): void
    {
        $this->proxy->setAlternativeService($this->alternative);
        $this->proxy->restoreOriginalService();

        $this->assertEquals($this->original->getValue(), $this->proxy->getValue());
    }
}

class FakeService
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
