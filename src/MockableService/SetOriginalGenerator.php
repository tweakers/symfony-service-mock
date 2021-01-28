<?php

namespace Tweakers\Test\MockableService;

use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use ProxyManager\Generator\MethodGenerator;

/**
 * Generator for a setOriginal-method that sets the 'base' service which is used by default within the proxy.
 *
 * It stores both a reference for a later restore-call as the value that is used.
 *
 * It generates this method:
 *
 * public function setOriginalService(object $original) : void
 * {
 *   $this->originalService = $original;
 *   $this->valueHolder = $original;
 * }
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class SetOriginalGenerator extends MethodGenerator
{
    public function __construct(PropertyGenerator $original, PropertyGenerator $valueHolder)
    {
        parent::__construct('setOriginalService');

        $originalParameter = new ParameterGenerator('original');
        $originalParameter->setType('object');

        $this->setParameter($originalParameter);
        $this->setReturnType('void');
        $this->setDocBlock('{@inheritDoc}');

        $body = '$this->' . $original->getName() . ' = $original;
$this->' . $valueHolder->getName() . ' = $original;';

        $this->setBody($body);
    }
}
