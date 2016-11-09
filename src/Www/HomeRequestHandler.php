<?php declare(strict_types = 1);

namespace EventSourcing\Www;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

final class HomeRequestHandler implements HandlesGetRequest
{
    public function handle(ProvidesReadRequestData $request)
    {
        echo "<h1>Hello! I'm an event sourced system.</h1>";
    }
}
