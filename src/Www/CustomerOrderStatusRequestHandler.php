<?php declare(strict_types = 1);

namespace EventSourcing\Www;

use EventSourcing\Read\CustomerOrderStorage;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

final class CustomerOrderStatusRequestHandler implements HandlesGetRequest
{
    /** @var CustomerOrderStorage */
    private $customerOrderStorage;

    public function __construct(CustomerOrderStorage $customerOrderStorage)
    {
        $this->customerOrderStorage = $customerOrderStorage;
    }

    public function handle(ProvidesReadRequestData $request)
    {
        $customerOrderId = $request->getInput()->get('customerOrderId');
        $customerOrder = $this->customerOrderStorage->findBy($customerOrderId);

        echo '<h1>Order #' . $customerOrderId . '</h1>'.
            '<h2>Status: ' . $customerOrder->getStatus() . '</h2>'.
            '<h3>Last update: ' . $customerOrder->getUpdatedAt()->format('Y-m-d H:i:s') . '</h3>';
    }
}
