<?php

namespace EventSourcing\Write;

use EventSourcing\Read\Reader;

final class EventPublisher
{
    private $readers = [];

    public function addReader(Reader $reader)
    {
        $this->readers[] = $reader;
    }

    public function publish($events)
    {
        foreach ($this->readers as $reader) {
            $reader->read($events);
        }
    }
}
