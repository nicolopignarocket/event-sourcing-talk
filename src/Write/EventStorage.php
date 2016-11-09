<?php

namespace EventSourcing\Write;

final class EventStorage
{
    public function __construct($storageDir)
    {
        $this->storageDir = $storageDir;
    }

    public function append($streamId, $events)
    {
        $streamPath = $this->storageDir . '/' . $streamId;
        touch($streamPath);

        $stream = json_decode(
            file_get_contents($streamPath),
            true
        );

        /** @var Event $event */
        foreach ($events as $event) {
            $stream[] = ['type' => get_class($event), 'payload' => $event->toArray()];
        }

        file_put_contents(
            $streamPath,
            json_encode($stream)
        );
    }

    public function load($streamId)
    {
        $streamPath = $this->storageDir . '/' . $streamId;

        $stream = json_decode(
            file_get_contents($streamPath),
            true
        );

        $events = [];

        foreach ($stream as $event) {
            /** @var Event $eventClass */
            $eventClass = $event['type'];
            $eventPayload = $event['payload'];

            $events[] = $eventClass::fromArray($eventPayload);
        }

        return $events;
    }
}
