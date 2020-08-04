# coding=utf8
'''
发布／订阅
Publish/Subscribe
sender
https://github.com/rabbitmq/rabbitmq-tutorials
https://www.rabbitmq.com/tutorials/tutorial-three-python.html
          /[][][]
(P) -> {X}
          \[][][]

'''
import pika
import sys

# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')

connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()
# 创建一个fanout类型的交换机，命名为logs
# 扇型交换机（fanout exchange），它把消息发送给它所知道的所有队列
channel.exchange_declare(exchange='logs', exchange_type='fanout')

message = '_'.join(sys.argv[1:]) or 'hello publish!'
channel.basic_publish(exchange='logs', routing_key='', body=message)

connection.close()
