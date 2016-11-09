<?php

namespace EventSourcing\Read;

final class CustomerOrder
{
    /** @var string */
    private $customerOrderId;

    /** @var string */
    private $status;

    /** @var \DateTimeImmutable */
    private $updatedAt;

    public static function fromStorage(array $storageData): self
    {
        $customerOrder = new self($storageData['customerOrderId']);
        $customerOrder->status = $storageData['status'];
        $customerOrder->updatedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $storageData['updatedAt']);

        return $customerOrder;
    }

    public function __construct(string $customerOrderId)
    {
        $this->customerOrderId = $customerOrderId;
    }

    public function updateStatus(string $status, \DateTimeImmutable $updatedAt)
    {
        $this->status = $status;
        $this->updatedAt = $updatedAt;
    }

    public function getCustomerOrderId(): string
    {
        return $this->customerOrderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
