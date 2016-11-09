<?php declare(strict_types = 1);

namespace EventSourcing\Www;

use EventSourcing\Read\CustomerOrderStorage;
use EventSourcing\Write\EventPublisher;
use EventSourcing\Write\EventStorage;
use IceHawk\IceHawk\Defaults\IceHawkConfig;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\WriteRoute;

final class RouteConfiguration extends IceHawkConfig
{
    /** @var CustomerOrderStorage */
    private $customerOrderStorage;

    /** @var EventStorage */
    private $eventStorage;

    /** @var EventPublisher */
    private $eventPublisher;

    public function __construct(
        CustomerOrderStorage $customerOrderStorage,
        EventStorage $eventStorage,
        EventPublisher $eventPublisher
    ) {
        $this->customerOrderStorage = $customerOrderStorage;
        $this->eventStorage = $eventStorage;
        $this->eventPublisher = $eventPublisher;
    }

    public function getReadRoutes()
    {
        return [
            new ReadRoute(
                new Literal('/'),
                new HomeRequestHandler()
            ),
            new ReadRoute(
                new RegExp('~^/user/order-status/(.+)$~', ['customerOrderId']),
                new CustomerOrderStatusRequestHandler($this->customerOrderStorage)
            )
        ];
    }

    public function getWriteRoutes()
    {
        return [
            new WriteRoute(
                new Literal('/import-order'),
                new ImportOrderRequestHandler($this->eventStorage, $this->eventPublisher)
            ),
            new WriteRoute(
                new RegExp('~^/set-ready-to-ship/(.+)$~', ['incomingOrderId']),
                new SetReadyToShipRequestHandler($this->eventStorage, $this->eventPublisher)
            ),
        ];
    }
}
