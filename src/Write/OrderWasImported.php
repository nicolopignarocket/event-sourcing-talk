<?php

namespace EventSourcing\Write;

final class OrderWasImported implements Event
{
    /** @var IncomingOrderId */
    private $incomingOrderId;

    /** @var \DateTimeImmutable */
    private $at;

    public static function fromArray($array): Event
    {
        return new self(
            new IncomingOrderId($array['incomingOrderId']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $array['at'])
        );
    }

    public function __construct(IncomingOrderId $orderId, \DateTimeImmutable $at)
    {
        $this->incomingOrderId = $orderId;
        $this->at = $at;
    }

    public function getIncomingOrderId(): IncomingOrderId
    {
        return $this->incomingOrderId;
    }

    public function getAt(): \DateTimeImmutable
    {
        return $this->at;
    }

    public function toArray(): array
    {
        return [
            'incomingOrderId' => $this->incomingOrderId->toString(),
            'at' => $this->at->format('Y-m-d H:i:s')
        ];
    }
}
