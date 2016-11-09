<?php

namespace EventSourcing\Read;

interface Reader
{
    public function read(array $events);
}
