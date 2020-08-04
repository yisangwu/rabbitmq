# coding=utf8
"""
Work Queues
worker receive message

https://www.rabbitmq.com/tutorials/tutorial-two-python.html
https://rabbitmq.mr-ping.com/tutorials_with_python/[2]Work_Queues.html

工作队列（又称：任务队列——Task Queues）
是为了避免等待一些占用大量资源、时间的操作。当我们把任务（Task）当作消息发送到队列中，
一个运行在后台的工作者（worker）进程就会取出任务然后处理。当你运行多个工作者（workers），
任务就会在它们之间共享。
                 /(C1)
(P) -> [][][][][]
                 \(C2)
"""

import time
import pika


# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(pika.ConnectionParameters(
    host='192.168.50.83', credentials=credentials))

channel = connection.channel()
channel.queue_declare(queue='queues_py_x', durable=True)


def callback(ch, method, properties, body):
    """
    callback
    Arguments:
            channel: pika.Channel
            method: pika.spec.Basic.Deliver
            properties: pika.spec.BasicProperties
            body: bytes
    """
    print(" [x] Received %r" % body)
    time.sleep(body.count(b'.'))
    print(" [x] Done")

    '''
    为了防止消息丢失，RabbitMQ提供了消息响应（acknowledgments）。消费者会通过一个ack（响应），
    告诉RabbitMQ已经收到并处理了某条消息，然后RabbitMQ就会释放并删除这条消息。
    如果消费者（consumer）挂掉了，没有发送响应，
    RabbitMQ就会认为消息没有被完全处理，然后重新发送给其他消费者（consumer）。
    这样，及时工作者（workers）偶尔的挂掉，也不会丢失消息。
    '''
    ch.basic_ack(delivery_tag=method.delivery_tag)

# 使用basic.qos方法，并设置prefetch_count=1。这样是告诉RabbitMQ，在同一时刻，
# 不要发送超过1条消息给一个工作者（worker），直到它已经处理了上一条消息并且作出了响应。
channel.basic_qos(prefetch_count=1)
channel.basic_consume(queue='queues_py_x', on_message_callback=callback)
channel.start_consuming()


'''
>python queues-worker.py
 [x] Received b'vvvvvvvvvvvvvv'
 [x] Done
 [x] Received b'bbbbbbbbbbbbbbbbbb'
 [x] Done
 [x] Received b'aaaaaaaaaaaaa'
 [x] Done
'''

'''
默认来说，RabbitMQ会按顺序得把消息发送给每个消费者（consumer）。
平均每个消费者都会收到同等数量得消息。这种发送消息得方式叫做——轮询（round-robin）。
试着添加三个或更多得工作者（workers）。
'''
