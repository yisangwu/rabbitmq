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
  # rabbitmqctl list_queues
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once __DIR__ .  DS. 'vendor'.DS.'autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection('192.168.50.83', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);
echo " [*] Waiting for messages. To exit press CTRL+C", PHP_EOL;

$callback = function ($msg){
    printf('Received:%s'.PHP_EOL, $msg->body);
};


$channel->basic_consume('hello', '', false, true, false, false, $callback);

while($channel->is_consuming()){
    $channel->wait();
}

/*
php -f receive.php
 [*] Waiting for messages. To exit press CTRL+C
Received:Hello World!
Received:Hello World!
Received:Hello World!
Received:Hello World!
Received:Hello World!
*/
 
