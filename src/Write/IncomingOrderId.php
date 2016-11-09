<?php

namespace EventSourcing\Write;

final class IncomingOrderId
{
    /** @var string */
    private $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString()
    {
        return $this->uuid;
    }
}
