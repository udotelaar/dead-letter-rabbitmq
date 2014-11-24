<?php

date_default_timezone_set('Europe/Berlin');

$app = new Silex\Application();

$app->register(new \fiunchinho\Silex\Provider\RabbitServiceProvider(), array(
    'rabbit.connections' => [
        'default' => [
            'host'      => 'localhost',
            'port'      => 5672,
            'user'      => 'guest',
            'password'  => 'guest',
            'vhost'     => '/'
        ]
    ],
    'rabbit.producers' => [
        'MessageProcessor' => [
            'connection'        => 'default',
            'exchange_options'  => ['name' => 'MessageProcessor', 'type' => 'direct'],
            'queue_options'     => ['name' => 'MessageProcessor_Delay', 'arguments' =>  ['x-message-ttl' => ['I', '10000'], 'x-dead-letter-exchange' => ['S', 'amq.direct'], 'x-dead-letter-routing-key' => ['S', 'MessageProcessor']]],
        ],
    ],
    'rabbit.consumers' => [
        'MessageProcessor' => [
            'connection'        => 'default',
            'exchange_options'  => ['name' => 'MessageProcessor', 'type' => 'direct'],
            'queue_options'     => ['name' => 'MessageProcessor'],
            'callback'          => 'messageprocessor'
        ]
    ]
));


$app->get('/test', function () use ($app) {

    $producer = $app['rabbit.producer']['MessageProcessor'];
    $producer->publish('A message!');

    return 'done';

});

return $app;
