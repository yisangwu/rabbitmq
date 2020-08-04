# coding=utf8
"""
rabbitmq received
https://www.rabbitmq.com/tutorials/tutorial-one-python.html

        hello
(P) ->[][][][][] -> (C)
 # rabbitmqctl list_queues
"""

import pika

credentials = pika.PlainCredentials('admin', 'admin')

connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()

# 确认队列是存在，创建同一个队列
channel.queue_declare(queue='hello-py')


def callback(ch, method, properties, body):
    """
        为队列定义一个回调（callback）函数

        Arguments:
                channel: pika.Channel
                method: pika.spec.Basic.Deliver
                properties: pika.spec.BasicProperties
                body: bytes
    """
    print('ch:{}, method:{}, properties:{}, body:{}'.format(
        ch, method, properties, body))

# http://www.rabbitmq.com/confirms.html
channel.basic_consume(
    queue='hello-py', on_message_callback=callback, auto_ack=False)

channel.start_consuming()


'''
ch:<BlockingChannel impl=<Channel number=1 OPEN conn=<SelectConnection OPEN transport=<pika.adapters.utils.io_services_utils._AsyncPlaintextTransport object at 0x000001FF0A801370> params=<ConnectionParameters host=192.168.50.83 port=5672 virtual_host=/ ssl=False>>>>, 
method:<Basic.Deliver(['consumer_tag=ctag1.76d63f08605a4348acf390602225e476', 'delivery_tag=1', 'exchange=', 'redelivered=False', 'routing_key=hello-py'])>, 
properties:<BasicProperties>,
body:b'Hello World Python!'

ch:<BlockingChannel impl=<Channel number=1 OPEN conn=<SelectConnection OPEN transport=<pika.adapters.utils.io_services_utils._AsyncPlaintextTransport object at 0x000001FF0A801370> params=<ConnectionParameters host=192.168.50.83 port=5672 virtual_host=/ ssl=False>>>>, 
method:<Basic.Deliver(['consumer_tag=ctag1.76d63f08605a4348acf390602225e476', 'delivery_tag=2', 'exchange=', 'redelivered=False', 'routing_key=hello-py'])>, 
properties:<BasicProperties>, 
body:b'Hello World Python!'
'''
