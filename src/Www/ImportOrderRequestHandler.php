<?php declare(strict_types = 1);

namespace EventSourcing\Www;

use EventSourcing\Write\EventPublisher;
use EventSourcing\Write\EventStorage;
use EventSourcing\Write\IncomingOrder;
use EventSourcing\Write\IncomingOrderId;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use Ramsey\Uuid\Uuid;

final class ImportOrderRequestHandler implements HandlesPostRequest
{
    /** @var EventStorage */
    private $eventStorage;

    /** @var EventPublisher */
    private $eventPublisher;

    public function __construct(EventStorage $eventStorage, EventPublisher $eventPublisher)
    {
        $this->eventStorage = $eventStorage;
        $this->eventPublisher = $eventPublisher;
    }

    public function handle(ProvidesWriteRequestData $request)
    {
        // simulating order import
        $incomingOrderId = Uuid::uuid4();

        $incomingOrder = IncomingOrder::import(
            new IncomingOrderId($incomingOrderId->toString()),
            new \DateTimeImmutable('now')
        );

        $events = $incomingOrder->flushRecordedEvents();
        $this->eventStorage->append($incomingOrderId, $events);

        $this->eventPublisher->publish($events);

        echo '<h1>Order #' . $incomingOrderId->toString() . ' was imported</h1>';
    }
}
