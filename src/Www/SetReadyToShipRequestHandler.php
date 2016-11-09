<?php declare(strict_types = 1);

namespace EventSourcing\Www;

use EventSourcing\Write\EventPublisher;
use EventSourcing\Write\EventStorage;
use EventSourcing\Write\IncomingOrder;
use EventSourcing\Write\IncomingOrderHistory;
use EventSourcing\Write\IncomingOrderId;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use Ramsey\Uuid\Uuid;

final class SetReadyToShipRequestHandler implements HandlesPostRequest
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
        try {
            $incomingOrderId = $request->getInput()->get('incomingOrderId');
            $events = $this->eventStorage->load($incomingOrderId);

            $incomingOrderId = new IncomingOrderId($incomingOrderId);
            $incomingOrder = IncomingOrder::reconstituteFrom(
                new IncomingOrderHistory($incomingOrderId, $events)
            );
            $incomingOrder->setReadyToShip(
                new \DateTimeImmutable('now')
            );

            $events = $incomingOrder->flushRecordedEvents();
            $this->eventStorage->append($incomingOrderId->toString(), $events);

            $this->eventPublisher->publish($events);

            echo '<h1>Order #' . $incomingOrderId->toString() . ' was set ready to ship';
        } catch (\DomainException $domainException) {
            echo '<h1>' . $domainException->getMessage() .'</h1>';
        }
    }
}
