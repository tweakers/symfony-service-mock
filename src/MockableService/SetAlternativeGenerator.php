<?php

namespace Tweakers\Test\MockableService;

use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use ProxyManager\Generator\MethodGenerator;

/**
 * Generator for a setAlternative-method that allows to use a different service (normally a test double) within the proxy.
 *
 * It generates this method:
 *
 * public function setAlternativeService(object $alternative) : void
 * {
 *   $this->valueHolder = $alternative;
 * }
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class SetAlternativeGenerator extends MethodGenerator
{
    public function __construct(PropertyGenerator $valueHolder)
    {
        parent::__construct('setAlternativeService');

        $alternativeParameter = new ParameterGenerator('alternative');
        $alternativeParameter->setType('object');

        $this->setParameter($alternativeParameter);
        $this->setReturnType('void');
        $this->setDocBlock('{@inheritDoc}');

        $this->setBody('$this->' . $valueHolder->getName() . ' = $alternative;');
    }
}
