<?php

namespace Tweakers\Test\MockableService;

use ProxyManager\Generator\Util\UniqueIdentifierGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Generator for the originalService-property.
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
class OriginalPropertyGenerator extends PropertyGenerator
{
    public function __construct()
    {
        parent::__construct(UniqueIdentifierGenerator::getIdentifier('originalService'));

        $this->setVisibility(self::VISIBILITY_PRIVATE);
        $this->setDocBlock('@var \\object|null reference to the original service');
    }
}
