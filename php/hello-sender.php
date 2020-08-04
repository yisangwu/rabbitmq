<?php
/**
 * rabbitmq
 * received
 * 
 * https://www.rabbitmq.com/tutorials/tutorial-one-php.html
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/ying-yong-jiao-cheng/php-ban/1-hello_world
 * https://github.com/rabbitmq/rabbitmq-tutorials/blob/master/php/send.php
 *
 *        hello
 *(P) ->[][][][][] -> (C)
 *
 * # rabbitmqctl list_queues
 * 
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ .  DS. 'vendor'.DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


# 建立一个到RabbitMQ服务器的连接
$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建一个名为"hello"的队列用来将消息投递进去
$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Hello World!');

# 空字符串代表默认或者匿名交换机
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();