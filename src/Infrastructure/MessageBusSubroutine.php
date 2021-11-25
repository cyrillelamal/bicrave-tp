<?php

namespace App\Infrastructure;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessageBusSubroutine implements SubroutineInterface
{
    private MessageBusInterface $bus;

    public function __construct(
        MessageBusInterface $bus,
    )
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(object $message): mixed
    {
        $envelop = $this->bus->dispatch($message);

        $stamp = $envelop->last(HandledStamp::class);

        return $stamp instanceof HandledStamp ? $stamp->getResult() : null;
    }
}
