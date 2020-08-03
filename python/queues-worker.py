# coding=utf8
"""
Work Queues
worker receive message
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
    ch.basic_ack(delivery_tag=method.delivery_tag)

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
