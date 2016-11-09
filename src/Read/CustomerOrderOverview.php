<?php

namespace EventSourcing\Read;

use EventSourcing\Write\OrderWasImported;
use EventSourcing\Write\OrderWasSetReadyToShip;

final class CustomerOrderOverview implements Reader
{
    /** @var CustomerOrderStorage */
    private $customerOrderStorage;

    public function __construct(CustomerOrderStorage $customerOrderStorage)
    {
        $this->customerOrderStorage = $customerOrderStorage;
    }

    public function read(array $events) {
        foreach ($events as $event) {
            $this->projectEvent($event);
        }
    }

    private function projectEvent($event)
    {
        $eventClassReflection = new \ReflectionClass($event);
        $eventClass = $eventClassReflection->getShortName();
        $method = 'project' . $eventClass;

        $this->{$method}($event);
    }

    private function projectOrderWasImported(OrderWasImported $event)
    {
        $customerOrder = new CustomerOrder(
            $event->getIncomingOrderId()->toString()
        );
        $customerOrder->updateStatus(
            "We received your order",
            $event->getAt()
        );
        $this->customerOrderStorage->save($customerOrder);
    }

    private function projectOrderWasSetReadyToShip(OrderWasSetReadyToShip $event)
    {
        $customerOrder = $this->customerOrderStorage->findBy($event->getIncomingOrderId()->toString());
        $customerOrder->updateStatus(
            "Your order is ready to be shipped",
            $event->getAt()
        );
        $this->customerOrderStorage->save($customerOrder);
    }
}
