<?php

namespace EventSourcing\Write;

final class IncomingOrder
{
    /** @var IncomingOrderId */
    private $incomingOrderId;

    /** @var string */
    private $status;

    private $recordedEvents = [];

    public static function import(IncomingOrderId $incomingOrderId, \DateTimeImmutable $at): self
    {
        $incomingOrder = new self();

        $incomingOrder->recordThat(
            new OrderWasImported($incomingOrderId, $at)
        );

        return $incomingOrder;
    }

    public static function reconstituteFrom(IncomingOrderHistory $incomingOrderHistory): self
    {
        $incomingOrder = new self($incomingOrderHistory->getIncomingOrderId());
        foreach ($incomingOrderHistory->getEvents() as $event) {
            $incomingOrder->apply($event);
        }

        return $incomingOrder;
    }

    public function flushRecordedEvents()
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public function setReadyToShip(\DateTimeImmutable $at)
    {
        if ($this->status != 'incoming') {
            throw new \DomainException('Order should be in "incoming" status. Current status: "' . $this->status . '"');
        }

        $this->recordThat(
            new OrderWasSetReadyToShip($this->incomingOrderId, $at)
        );
    }

    private function __construct()
    {
        $this->status = 'incoming';
    }

    private function recordThat($event)
    {
        $this->recordedEvents[] = $event;
        $this->apply($event);
    }

    private function apply($event)
    {
        $eventClassReflection = new \ReflectionClass($event);
        $eventClass = $eventClassReflection->getShortName();
        $method = 'apply' . $eventClass;

        $this->{$method}($event);
    }

    private function applyOrderWasImported(OrderWasImported $event)
    {
        $this->incomingOrderId = $event->getIncomingOrderId();
    }

    private function applyOrderWasSetReadyToShip(OrderWasSetReadyToShip $event)
    {
        $this->status = 'ready to ship';
    }
}
