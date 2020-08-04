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

routing_key = sys.argv[1] if len(sys.argv) > 2 else 'someone.topic'
message = '-'.join(sys.argv[2:]) or 'hello topic!'


channel.basic_publish(exchange='topics_logs_x',
                      routing_key=routing_key, body=message)

connection.close()

'''
绑定多个队列
>python python\topics_emit.py 11.xx BBBB

>python python\topics_emit.py a.111 BBBB

>python python\topics_emit.py a.33 BBBB
'''

'''
绑定键为 * 的队列
>python python\topics_receive.py *
Topic received:a b'VVV'
'''

'''
绑定键为 # 的队列
>python python\topics_receive.py #
Topic received:a b'VVV'
Topic received:a.a b'VVV'
Topic received:a.a.a b'VVV'
Topic received:a.a.a.1 b'VVV'
'''
