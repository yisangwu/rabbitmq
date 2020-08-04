# coding=utf8
"""
Work Queues
task send message

工作队列（又称：任务队列——Task Queues）
是为了避免等待一些占用大量资源、时间的操作。当我们把任务（Task）当作消息发送到队列中，
一个运行在后台的工作者（worker）进程就会取出任务然后处理。当你运行多个工作者（workers），
任务就会在它们之间共享。
                 /(C1)
(P) -> [][][][][]
                 \(C2)
"""

import sys
import pika


# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(pika.ConnectionParameters(
    host='192.168.50.83', credentials=credentials))

channel = connection.channel()

# 为了不让队列消失，需要把队列声明为持久化（durable）
channel.queue_declare(queue='queues_py_x', durable=True)

message = ''.join(sys.argv[1:]) or 'hello work queues!'

# 消息也要设为持久化——将delivery_mode的属性设为2
channel.basic_publish(
    exchange='',
    routing_key='queues_py_x',
    body=message,
    properties=pika.BasicProperties(delivery_mode=2)
)

print('sent %r' % message)
connection.close()


'''
将消息设为持久化并不能完全保证不会丢失。以上代码只是告诉了RabbitMq要把消息存到硬盘，
但从RabbitMq收到消息到保存之间还是有一个很小的间隔时间。
因为RabbitMq并不是所有的消息都使用fsync(2)——它有可能只是保存到缓存中，
并不一定会写到硬盘中。并不能保证真正的持久化，
但已经足够应付我们的简单工作队列。
如果你一定要保证持久化，你需要改写你的代码来支持事务（transaction）。
'''


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
