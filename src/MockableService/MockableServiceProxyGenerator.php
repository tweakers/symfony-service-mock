<?php

namespace Tweakers\Test\MockableService;

use InvalidArgumentException;
use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use ProxyManager\Generator\Util\ClassGeneratorUtils;
use ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator;
use ReflectionClass;

/**
 * Generator for Proxy-classes that allows switching the underlying implementation.
 *
 * The intended use is in conjunction with services that need to be 'mocked' (or otherwise replaced by a test double) during testing.
 *
 * It generates a proxy that 'forwards' all calls to the original implementation by default,
 * but allows to set an alternative implementation (i.e. a mock, fake, etc). When a test is done, the proxy can be 'restored' to the original service.
 *
 * @note The only reason it extends the LazyLoading-proxy is simply because that already did most of the necessary work.
 *
 * Since the code from LazyLoadingValueHolderGenerator already has a property for the 'forward to'-service, that property is used to store the 'active' implementation.
 *
 * In addition to that property, it adds an 'originalService'-property to keep track of the 'default' service
 *
 * It also adds these methods to allow modifying the state:
 * 'setOriginalService' @see SetOriginalGenerator
 * 'setAlternativeService' @see SetAlternativeGenerator
 * 'restoreOriginalService' @see RestoreOriginalsGenerator
 *
 * @author Arjen
 */
class MockableServiceProxyGenerator extends LazyLoadingValueHolderGenerator
{
    public function generate(ReflectionClass $originalClass, ClassGenerator $classGenerator)
    {
        parent::generate($originalClass, $classGenerator);

        $implementedInterfaces   = $classGenerator->getImplementedInterfaces();
        $implementedInterfaces[] = MockableService::class;

        $classGenerator->setImplementedInterfaces($implementedInterfaces);

        $original    = new OriginalPropertyGenerator();
        $valueHolder = $this->findValueHolderProperty($classGenerator);

        $classGenerator->addPropertyFromGenerator($original);

        ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, new RestoreOriginalsGenerator($original, $valueHolder));
        ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, new SetAlternativeGenerator($valueHolder));
        ClassGeneratorUtils::addMethodIfNotFinal($originalClass, $classGenerator, new SetOriginalGenerator($original, $valueHolder));
    }

    private function findValueHolderProperty(ClassGenerator $classGenerator): PropertyGenerator
    {
        $properties = $classGenerator->getProperties();
        foreach ($properties as $property) {
            if (strpos($property->getName(), 'valueHolder') === 0) {
                return $property;
            }
        }

        throw new InvalidArgumentException('No value holder defined!');
    }
}
