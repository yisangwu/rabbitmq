<?php

/**
 * rabbitmq
 * Work Queues
 * worker receive message.
 * https://www.rabbitmq.com/tutorials/tutorial-two-php.html
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/
 *
 * 工作队列（又称：任务队列——Task Queues）是为了避免等待一些占用大量资源、时间的操作。
 * 当我们把任务（Task）当作消息发送到队列中，一个运行在后台的工作者（worker）进程就会取出任务然后处理。
 * 当你运行多个工作者（workers），任务就会在它们之间共享。
                 /(C1)
(P) -> [][][][][]
                 \(C2)

 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ .  DS. 'vendor'.DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();


$channel->queue_declare('queue_php', false, true, false, false);

/*
为了防止消息丢失，RabbitMQ提供了消息响应（acknowledgments）。消费者会通过一个ack（响应），告诉RabbitMQ已经收到并处理了某条消息，然后RabbitMQ就会释放并删除这条消息。
如果消费者（consumer）挂掉了，没有发送响应，RabbitMQ就会认为消息没有被完全处理，然后重新发送给其他消费者（consumer）。这样，及时工作者（workers）偶尔的挂掉，也不会丢失消息。
 */
$callback = function ($msg){
    printf('Received: %s'.PHP_EOL, $msg->body);
    sleep(mt_rand(1,2));
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// 使用basic.qos方法，并设置prefetch_count=1。这样是告诉RabbitMQ，
// 在同一时刻，不要发送超过1条消息给一个工作者（worker），
// 直到它已经处理了上一条消息并且作出了响应。
$channel->basic_qos(null, 1, null);
$channel->basic_consume('queue_php', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();


/*
默认来说，RabbitMQ会按顺序得把消息发送给每个消费者（consumer）。平均每个消费者都会收到同等数量得消息。这种发送消息得方式叫做——轮询（round-robin）。试着添加三个或更多得工作者（workers）。
 */