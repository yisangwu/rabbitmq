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
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建主题交换机（topic exchange）
$channel->exchange_declare('topic_logs', 'topic', false, false, false);
$routing_key = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'someone.topic';

$data = implode('-', array_slice($argv, 2));
if (empty($data)) {
    $data = 'hello topic!';
}
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'topic_logs', $routing_key);
$channel->close();
$connection->close();


/*
绑定多个
php -f php\topic_receive.php  a.*  *.xx
Topic receive:a.1 1
Topic receive:a.xx 1
Topic receive:aaa.xx 1
Topic receive:aaa.xx 1wwww

绑定 #
>php -f php\topic_receive.php  #
Topic receive:a.xx 1
Topic receive:a.1 1

 */