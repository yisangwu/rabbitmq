<?php

// https://www.rabbitmq.com/tutorials/tutorial-one-php.html

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C", PHP_EOL;

$callback = function ($msg){
    printf('Received:%s'.PHP_EOL, $msg->body);
};


$channel->basic_consume('hello', '', false, true, false, false, $callback);

while($channel->is_consuming()){
    $channel->wait();
}

/*
php -f receive.php
 [*] Waiting for messages. To exit press CTRL+C
Received:Hello World!
Received:Hello World!
Received:Hello World!
Received:Hello World!
Received:Hello World!
*/
 
