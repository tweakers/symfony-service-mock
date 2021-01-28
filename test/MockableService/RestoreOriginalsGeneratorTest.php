<?php

namespace Tweakers\Test\MockableService;

use Laminas\Code\Generator\PropertyGenerator;
use PHPUnit\Framework\TestCase;

class RestoreOriginalsGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_the_reset_method(): void
    {
        $original    = new PropertyGenerator('original');
        $valueHolder = new PropertyGenerator('valueHolder');

        $generator = new RestoreOriginalsGenerator($original, $valueHolder);

        $output = $generator->generate();

        $expected = <<<'END'
    /**
     * {@inheritDoc}
     */
    public function restoreOriginalService() : void
    {
        $this->valueHolder = $this->original;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
