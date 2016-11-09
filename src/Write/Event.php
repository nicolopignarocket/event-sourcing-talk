<?php

namespace EventSourcing\Write;

interface Event
{
    public static function fromArray($array): self;
    public function toArray(): array;
}
