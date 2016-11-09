<?php

namespace EventSourcing\Read;

final class CustomerOrderStorage
{
    /** @var string */
    private $storagePath;

    public function __construct(string $storagePath)
    {
        $this->storagePath = $storagePath;
    }

    public function save(CustomerOrder $customerOrder)
    {
        $orderPath = $this->storagePath . '/' . $customerOrder->getCustomerOrderId();
        touch($orderPath);

        // no append here!
        file_put_contents(
            $orderPath,
            json_encode([
                'customerOrderId' => $customerOrder->getCustomerOrderId(),
                'status' => $customerOrder->getStatus(),
                'updatedAt' => $customerOrder->getUpdatedAt()->format('Y-m-d H:i:s')
            ])
        );
    }

    public function findBy(string $customerOrderId)
    {
        $orderPath = $this->storagePath . '/' . $customerOrderId;

        // no append here!
        $jsonData = json_decode(
            file_get_contents($orderPath),
            true
        );

        return CustomerOrder::fromStorage($jsonData);
    }
}
