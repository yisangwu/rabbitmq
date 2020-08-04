<?php

/**
 * rabbitmq
 * Work Queues
 * task send message 
 * https://www.rabbitmq.com/tutorials/tutorial-two-php.html
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/
 *
 * 工作队列（又称：任务队列——Task Queues）是为了避免等待一些占用大量资源、时间的操作。
 * 当我们把任务（Task）当作消息发送到队列中，一个运行在后台的工作者（worker）进程就会取出任务然后处理。
 * 当你运行多个工作者（workers），任务就会在它们之间共享。
                 /(C1)
(P) -> [][][][][]
                 \(C2)
 * 
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ .  DS. 'vendor'.DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('queue_php', false, true, false, false);

$data = implode(' ',array_slice($argv, 1));
if (empty($data)) {
    $data = 'hello word queues php!';
}


$msg = new AMQPMessage($data, array('delivery_mode'=> AMQPMessage::DELIVERY_MODE_PERSISTENT));   # 使消息持久化

$channel->basic_publish($msg, '', 'queue_php');

$channel->close();
$connection->close();

/*
将消息设为持久化并不能完全保证不会丢失。以上代码只是告诉了RabbitMq要把消息存到硬盘，
但从RabbitMq收到消息到保存之间还是有一个很小的间隔时间。
因为RabbitMq并不是所有的消息都使用fsync(2)——它有可能只是保存到缓存中，
并不一定会写到硬盘中。并不能保证真正的持久化，
但已经足够应付我们的简单工作队列。
如果你一定要保证持久化，你需要改写你的代码来支持事务（transaction）。
*/