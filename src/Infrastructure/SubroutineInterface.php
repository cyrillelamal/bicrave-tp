<?php

namespace App\Infrastructure;

/**
 * Each subroutine performs a specific task, packaged as a unit.
 * Implementations can be synchronous or asynchronous.
 *
 * @template T
 * @template V
 */
interface SubroutineInterface
{
    /**
     * Execute the subroutine.
     *
     * @param T $message the container of subroutine arguments.
     * @return V any value.
     */
    public function execute(object $message): mixed;
}
