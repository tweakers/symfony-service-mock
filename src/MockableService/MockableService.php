<?php

namespace Tweakers\Test\MockableService;

/**
 * Interface for a service-proxy that has a 'original' service and optional 'alternative' service.
 *
 * @author Arjen van der Meijden <acm@tweakers.net>
 */
interface MockableService
{
    /**
     * Use the given mock/fake/etc 'alternative' implementation, instead of the 'original' service.
     */
    public function setAlternativeService(object $alternative): void;

    /**
     * Use this service as 'original' service.
     *
     * Note: this method is only intended to be called once, at start (i.e. in a service container).
     */
    public function setOriginalService(object $original): void;

    /**
     * Remove the 'alternative' service and revert to 'original' service.
     */
    public function restoreOriginalService(): void;
}
