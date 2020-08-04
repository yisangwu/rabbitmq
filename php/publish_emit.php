<?php

/**
 * rabbitmq
 * 发布／订阅
 * Publish/Subscribe
 * sender
 * https://www.rabbitmq.com/tutorials/tutorial-three-php.html
 * 
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/ying-yong-jiao-cheng/php-ban/3-publish_subscribe.md
 
          /[][][]
(P) -> {X}
          \[][][]

 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__. DS . 'vendor' .DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

$data = implode('...', array_slice($argv, 1));
if (empty($data)) {
    $data = 'hello publish,subscribe!';
}

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'logs');

$channel->close();
$connection->close();
