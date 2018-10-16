<?php

namespace Tweakers\Test\MockableService;

use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\PropertyGenerator;

class ResetGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_the_reset_method()
    {
        $original    = new PropertyGenerator('original');
        $valueHolder = new PropertyGenerator('valueHolder');

        $generator = new ResetGenerator($original, $valueHolder);

        $output = $generator->generate();

        $expected = <<<'END'
    /**
     * {@inheritDoc}
     */
    public function reset() : void
    {
        $this->valueHolder = $this->original;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
