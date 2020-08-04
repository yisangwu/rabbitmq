<?php
/*
Topics
主题交换机
基于多个标准执行路由操作
https://www.rabbitmq.com/tutorials/tutorial-five-php.html
https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/ying-yong-jiao-cheng/php-ban/5-topics

主题交换机背后的逻辑跟直连交换机很相似 —— 一个携带着特定路由键的消息会被主题交换机投递给绑定键与之想匹配的队列。
但是它的绑定键和路由键有两个特殊应用方式：
* (星号) 用来表示一个单词.
# (井号) 用来表示任意数量（零个或多个）单词。

当一个队列的绑定键为 "#"（井号） 的时候，这个队列将会无视消息的路由键，接收所有的消息。
当 * (星号) 和 # (井号) 这两个特殊字符都未在绑定键中出现的时候，此时主题交换机就拥有的直连交换机的行为。
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__. DS . 'vendor' .DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建主题交换机（topic exchange）
$channel->exchange_declare('topic_logs', 'topic', false, false, false);

# 定义临时队列
# 当与消费者（consumer）断开连接的时候，这个队列被立即删除
list($queue_name, ,) = $channel->queue_declare('', false, false, true, false);


$binding_keys = array_slice($argv, 1);
if (empty($binding_keys)) {
    file_put_contents('php://stderr', 'Topic Usage:php -f xx.php [key] message');
    exit(0);
}

# 绑定队列到交换机
foreach($binding_keys as $binding_key){
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
}


$callback = function ($msg){
    printf(
        'Topic receive:%s %s'.PHP_EOL, 
        $msg->delivery_info['routing_key'], 
        $msg->body
    );
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();

/*
绑定多个

>php -f  php\topic_emit.php  a.1 1
>php -f  php\topic_emit.php  a.xx 1
>php -f  php\topic_emit.php  aaa.xx 1
>php -f  php\topic_emit.php  aaa.xx.ww 1 被忽略
>php -f  php\topic_emit.php  aaa.xx 1wwww


绑定#

>php -f  php\topic_emit.php  a.xx 1
>php -f  php\topic_emit.php  a.1 1

 */