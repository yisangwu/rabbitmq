<?php
/**
 * rabbbitmq
 * 路由(Routing)
 * receive
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/ying-yong-jiao-cheng/php-ban/4-routing.md
 *
 * https://www.rabbitmq.com/tutorials/tutorial-four-php.html
 *        error
           ---- [][][][][] -> (C1)
          /
(P) -> {x}-----
         |     \
         |----- [][][][][] -> (C2)
直连交换机（direct exchange）:
交换机将会对绑定键（binding key）和路由键（routing key）进行精确匹配，从而确定消息该分发到哪个队列

 */


/*
php -f php\routing_receive.php warning 接收单个
php -f php\routing_receive.php warning info  接收多个
[Routing Receive]:warning,VVVVVV--1111111111111111
[Routing Receive]:info,VVVVVV--1111111111111111
 */


defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__. DS . 'vendor' .DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建一个直连交换机（Direct exchange）
$channel->exchange_declare('direct_logs', 'direct', false, false, false);
# 队列名，以日志级别命名
$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';


# 定义临时队列
# 当与消费者（consumer）断开连接的时候，这个队列被立即删除
list($queue_name, ,) = $channel->queue_declare('', false, false, true, false);

$severities = array_slice($argv, 1);
if (empty($severities)) {
    file_put_contents('php://stderr', 'Usage: php -f xx.php [info/waring/error] aaaa'.PHP_EOL);
}

foreach($severities as $severity){
    $channel->queue_bind($queue_name, 'direct_logs', $severity);
}

$callback = function ($msg){
    echo '[Routing Receive]:', $msg->delivery_info['routing_key'], ',', $msg->body,PHP_EOL;
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
