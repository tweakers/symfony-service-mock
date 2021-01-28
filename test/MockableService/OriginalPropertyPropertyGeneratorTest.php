<?php

namespace Tweakers\Test\MockableService;

use Laminas\Code\Generator\PropertyGenerator;
use PHPUnit\Framework\TestCase;

class OriginalPropertyPropertyGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_a_set_method(): void
    {
        $original    = new PropertyGenerator('original');
        $valueHolder = new PropertyGenerator('valueHolder');

        $generator = new SetOriginalGenerator($original, $valueHolder);

        $output = $generator->generate();

        $expected = <<<'END'
    /**
     * {@inheritDoc}
     */
    public function setOriginalService(object $original) : void
    {
        $this->original = $original;
        $this->valueHolder = $original;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
