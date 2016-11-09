<?php

use \Ramsey\Uuid\Uuid;
use \EventSourcing\Write\{IncomingOrder, IncomingOrderId};
use \EventSourcing\Write\EventStorage;
use \EventSourcing\Write\EventPublisher;
use \EventSourcing\Write\IncomingOrderHistory;
use \EventSourcing\Read\CustomerOrderOverview;
use \EventSourcing\Read\CustomerOrderStorage;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$eventStorage = new EventStorage(__DIR__ . '/../storage/event-stream');

$customerOrderStorage = new CustomerOrderStorage(__DIR__ . '/../storage/customer-order');
$customerOrderOverview = new CustomerOrderOverview($customerOrderStorage);

$eventPublisher = new EventPublisher();
$eventPublisher->addReader(
    new CustomerOrderOverview($customerOrderStorage)
);

$app->get('/', function() {
   return '<h1>Hello! I\'m an event sourced system!</h1>';
});

// Import new order
$app->get('/import-order', function() use ($app, $eventStorage, $eventPublisher) {
    // simulating order import
    $incomingOrderId = Uuid::uuid4();

    $incomingOrder = IncomingOrder::import(
        new IncomingOrderId($incomingOrderId),
        new DateTimeImmutable('now')
    );

    $events = $incomingOrder->flushRecordedEvents();
    $eventStorage->append($incomingOrderId, $events);

    $eventPublisher->publish($events);

    return '<h1>Order #' . $incomingOrderId->toString() . ' was imported</h1>';
});

// Set ready to ship an existing order
$app->get('/set-ready-to-ship/{id}', function($id) use($app, $eventStorage, $eventPublisher) {
    try {
        $events = $eventStorage->load($id);

        $incomingOrderId = new IncomingOrderId($id);
        $incomingOrder = IncomingOrder::reconstituteFrom(
            new IncomingOrderHistory($incomingOrderId, $events)
        );
        $incomingOrder->setReadyToShip(
            new \DateTimeImmutable('now')
        );

        $events = $incomingOrder->flushRecordedEvents();
        $eventStorage->append($id, $events);

        $eventPublisher->publish($events);

        return '<h1>Order #' . $id . ' was set ready to ship';
    } catch (\DomainException $domainException) {
        return '<h1>' . $domainException->getMessage() .'</h1>';
    }
});

$app->get('/user/order-status/{id}', function($id) use ($customerOrderStorage) {
    $customerOrder = $customerOrderStorage->findBy($id);

    return '<h1>Order #' . $id . '</h1>'.
           '<h2>Status: ' . $customerOrder->getStatus() . '</h2>'.
           '<h3>Last update: ' . $customerOrder->getUpdatedAt()->format('Y-m-d H:i:s') . '</h3>';
});

$app->run();
