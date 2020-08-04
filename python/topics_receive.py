# coding=utf8
'''
Topics
主题交换机
基于多个标准执行路由操作
https://rabbitmq.mr-ping.com/tutorials_with_python/[5]Topics.html
https://www.rabbitmq.com/tutorials/tutorial-five-python.html
https://github.com/rabbitmq/rabbitmq-tutorials/blob/master/python/receive_logs_topic.py

主题交换机背后的逻辑跟直连交换机很相似 —— 一个携带着特定路由键的消息会被主题交换机投递给绑定键与之想匹配的队列。
但是它的绑定键和路由键有两个特殊应用方式：
* (星号) 用来表示一个单词.
# (井号) 用来表示任意数量（零个或多个）单词。
'''

import sys
import pika


# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()
# 创建主题交换机（topic exchange）
channel.exchange_declare(exchange='topics_logs_x', exchange_type='topic')

# 定义临时队列
# 当与消费者（consumer）断开连接的时候，这个队列被立即删除
result = channel.queue_declare('', exclusive=True)
queue_name = result.method.queue

binding_keys = sys.argv[1:]
if not binding_keys:
    sys.stderr.write('Usge Topic:{} [binding_key] message'.format(sys.argv[0]))
    sys.exit(1)

# 绑定队列到交换机
for binding_key in binding_keys:
    channel.queue_bind(exchange='topics_logs_x',
                       queue=queue_name, routing_key=binding_key)


def callback(ch, method, properties, body):
    print('Topic received:{} {}'.format(method.routing_key, body))


channel.basic_consume(
    queue=queue_name, on_message_callback=callback, auto_ack=True)

channel.start_consuming()

'''
绑定多个队列
> python python\topics_receive.py *.xx  a.*
Topic received:11.xx b'BBBB'
Topic received:a.111 b'BBBB'
Topic received:a.33 b'BBBB'

'''

'''
绑定键为 * 的队列
>python python\topics_receive.py *
Topic received:a b'VVV'
'''

'''
绑定键为 #  的队列
>python python\topics_emit.py a.a VVV

>python python\topics_emit.py a.a.a VVV

>python python\topics_emit.py a.a.a.1 VVV

'''
