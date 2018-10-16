<?php

namespace Tweakers\Test\MockableService;

use ProxyManager\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Generator for a reset-method that restores the original service as 'the service to use' within the proxy.
 *
 * It generates this method:
 *
 * public function setOriginal(object $original) : void
 * {
 *   $this->originalService = $original;
 *   $this->valueHolder = $original;
 * }
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class ResetGenerator extends MethodGenerator
{
    public function __construct(PropertyGenerator $original, PropertyGenerator $valueHolder)
    {
        parent::__construct('reset');

        $this->setDocBlock('{@inheritDoc}');
        $this->setReturnType('void');

        $this->setBody('$this->' . $valueHolder->getName() . ' = $this->' . $original->getName() . ';');
    }
}
