<?php

namespace Tweakers\Test\MockableService;

use PHPUnit\Framework\TestCase;
use Zend\Code\Generator\PropertyGenerator;

class SetAlternativeGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_a_set_method()
    {
        $valueHolder = new PropertyGenerator('valueHolder');

        $generator = new SetAlternativeGenerator($valueHolder);

        $output = $generator->generate();

        $expected = <<<'END'
    /**
     * {@inheritDoc}
     */
    public function setAlternative(object $alternative) : void
    {
        $this->valueHolder = $alternative;
    }

END;

        $this->assertEquals($expected, $output);
    }
}
