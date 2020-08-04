<?php

/**
 * rabbitmq
 * 发布／订阅
 * Publish/Subscribe
 * received
 * https://github.com/rabbitmq/rabbitmq-tutorials
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

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建一个fanout类型的交换机，命名为logs
# 扇型交换机（fanout），它把消息发送给它所知道的所有队列
$channel->exchange_declare('logs', 'fanout', false, false, false);

# 定义临时队列
# 当与消费者（consumer）断开连接的时候，这个队列被立即删除
list($queue_name, ,) = $channel->queue_declare('', false, false, true, false);

# logs交换机将会把消息添加到我们的队列中
$channel->queue_bind($queue_name, 'logs');


$callback = function($msg){
    echo 'Subscribe:', $msg->body, PHP_EOL;
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while($channel->is_consuming()){
    $channel->wait();
}

$channel->close();
$connection->close();
