# coding=utf8
'''
发布／订阅
Publish/Subscribe
subscribe
https://github.com/rabbitmq/rabbitmq-tutorials
https://www.rabbitmq.com/tutorials/tutorial-three-python.html
          /[][][]
(P) -> {X}
          \[][][]
'''

import pika

# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')

connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()
# 创建一个fanout类型的交换机，命名为logs
# 扇型交换机（fanout exchange），它把消息发送给它所知道的所有队列
channel.exchange_declare(exchange='logs', exchange_type='fanout')

result = channel.queue_declare(queue='', exclusive=True)
queue_name = result.method.queue

channel.queue_bind(exchange='logs', queue=queue_name)


def callback(ch, method, properties, body):
    print('subscribe:{}'.format(body))

channel.basic_consume(
    queue=queue_name,
    on_message_callback=callback,
    auto_ack=True
)

channel.start_consuming()
