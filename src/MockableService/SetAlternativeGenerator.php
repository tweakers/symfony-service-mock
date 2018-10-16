<?php

namespace Tweakers\Test\MockableService;

use ProxyManager\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Generator for a setAlternative-method that allows to use a different service (normally a test double) within the proxy.
 *
 * It generates this method:
 *
 * public function setAlternative(object $alternative) : void
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
        parent::__construct('setAlternative');

        $alternativeParameter = new ParameterGenerator('alternative');
        $alternativeParameter->setType('object');

        $this->setParameter($alternativeParameter);
        $this->setReturnType('void');
        $this->setDocBlock('{@inheritDoc}');

        $this->setBody('$this->' . $valueHolder->getName() . ' = $alternative;');
    }
}
