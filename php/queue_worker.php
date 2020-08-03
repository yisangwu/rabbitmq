<?php

/**
 * rabbitmq
 * Work Queues
 * worker receive message
 */

require_once __DIR__ . '\vendor\autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();


$channel->queue_declare('queue_php', false, true, false, false);

$callback = function ($msg){
    printf('Received: %s'.PHP_EOL, $msg->body);
    sleep(mt_rand(1,2));
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('queue_php', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
