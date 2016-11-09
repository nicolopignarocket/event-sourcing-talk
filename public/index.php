<?php declare(strict_types = 1);

use \EventSourcing\Write\EventStorage;
use \EventSourcing\Write\EventPublisher;
use \EventSourcing\Read\CustomerOrderOverview;
use \EventSourcing\Read\CustomerOrderStorage;
use EventSourcing\Www\RouteConfiguration;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Defaults\IceHawkDelegate;

require_once __DIR__.'/../vendor/autoload.php';

$eventStorage = new EventStorage(__DIR__ . '/../storage/event-stream');

$customerOrderStorage = new CustomerOrderStorage(__DIR__ . '/../storage/customer-order');
$customerOrderOverview = new CustomerOrderOverview($customerOrderStorage);

$eventPublisher = new EventPublisher();
$eventPublisher->addReader(
    new CustomerOrderOverview($customerOrderStorage)
);

$app = new IceHawk(
    new RouteConfiguration(
        $customerOrderStorage,
        $eventStorage,
        $eventPublisher
    ),
    new IceHawkDelegate()
);
$app->init();
$app->handleRequest();
