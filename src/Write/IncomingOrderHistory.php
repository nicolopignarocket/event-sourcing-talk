<?php

namespace EventSourcing\Write;

final class IncomingOrderHistory
{
    /** @var IncomingOrderId */
    private $incomingOrderId;

    /** @var array */
    private $events;

    public function __construct(IncomingOrderId $incomingOrderId, array $events)
    {
        $this->incomingOrderId = $incomingOrderId;
        $this->events = $events;
    }

    public function getIncomingOrderId(): IncomingOrderId
    {
        return $this->incomingOrderId;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
