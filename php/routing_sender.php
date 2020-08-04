<?php

/**
 * rabbbitmq
 * 路由(Routing)
 * sender
 * https://xiaoxiami.gitbook.io/rabbitmq_into_chinese_php/ying-yong-jiao-cheng/php-ban/4-routing.md
 *
 * https://www.rabbitmq.com/tutorials/tutorial-four-php.html
 *        error
           ---- [][][][][] -> (C1)
          /
(P) -> {x}-----
         |     \
         |----- [][][][][] -> (C2)
 */

# php -f xx.php[0] info/error/warning[1] message11111[2]
# 
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__. DS . 'vendor' .DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

# 创建一个直连交换机（Direct exchange）
$channel->exchange_declare('direct_logs', 'direct', false, false, false);
# 队列名，以日志级别命名
$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';

$data =  implode('--', array_slice($argv, 2));
if (empty($data)) {
    $data = 'hello routing direct log!';
}

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'direct_logs', $severity);

$channel->close();
$connection->close();
