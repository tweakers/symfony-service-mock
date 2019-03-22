<?php

namespace Tweakers\Test\MockableService;

use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\PropertyGenerator;

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
    public function restoreOriginalServices() : void
    {
        $this->valueHolder = $this->original;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
