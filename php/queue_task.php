<?php

/**
 * rabbitmq
 * Work Queues
 * task send message 
 */

require_once __DIR__ . '\vendor\autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('queue_php', false, true, false, false);

$data = implode(' ',array_slice($argv, 1));
if (empty($data)) {
    $data = 'hello word queues php!';
}

$msg = new AMQPMessage($data, array('delivery_mode'=> AMQPMessage::DELIVERY_MODE_PERSISTENT));

$channel->basic_publish($msg, '', 'queue_php');

$channel->close();
$connection->close();