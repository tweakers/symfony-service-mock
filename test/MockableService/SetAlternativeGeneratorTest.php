<?php

namespace Tweakers\Test\MockableService;

use Laminas\Code\Generator\PropertyGenerator;
use PHPUnit\Framework\TestCase;

class SetAlternativeGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_a_set_method(): void
    {
        $valueHolder = new PropertyGenerator('valueHolder');

        $generator = new SetAlternativeGenerator($valueHolder);

        $output = $generator->generate();

        $expected = <<<'END'
    /**
     * {@inheritDoc}
     */
    public function setAlternativeService(object $alternative) : void
    {
        $this->valueHolder = $alternative;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
