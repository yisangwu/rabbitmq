# coding=utf8
"""
Work Queues
task send message
"""

import sys
import pika


# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(pika.ConnectionParameters(
    host='192.168.50.83', credentials=credentials))

channel = connection.channel()
channel.queue_declare(queue='queues_py_x', durable=True)

message = ''.join(sys.argv[1:]) or 'hello work queues!'
channel.basic_publish(
    exchange='',
    routing_key='queues_py_x',
    body=message,
    properties=pika.BasicProperties(delivery_mode=2)
)

print('sent %r' % message)
connection.close()


'''
>python queues-task.py 221341412421321321343232
sent '221341412421321321343232'

>python queues-task.py fassfaasf
sent 'fassfaasf'

>python queues-task.py fassfaasfsd1112
sent 'fassfaasfsd1112'

>python queues-task.py 434234324
sent '434234324'

>python queues-task.py bxcbewtgergw
sent 'bxcbewtgergw'
'''
