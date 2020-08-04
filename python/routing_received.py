# coding=utf8
'''
路由(Routing)
received
https://www.rabbitmq.com/tutorials/tutorial-four-python.html
https://rabbitmq.mr-ping.com/tutorials_with_python/[4]Routing.html
           error
           ---- [][][][][] -> (C1)
          /
(P) -> {x}-----
         |     \
         |----- [][][][][] -> (C2)
'''


import sys
import pika

# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()

channel.exchange_declare(exchange='direct_logs_x', exchange_type='direct')

# 定义临时队列
# 当与消费者（consumer）断开连接的时候，这个队列被立即删除
result = channel.queue_declare(queue='', exclusive=True)
queue_name = result.method.queue

severities = sys.argv[1:]
if not severities:
    sys.stderr.write('Usage: python xx.py [info/warning/error] aaaa')
    sys.exit(1)

for severity in severities:
    channel.queue_bind(exchange='direct_logs_x',
                       queue=queue_name, routing_key=severity)


def callback(ch, method, properties, body):
    print('[Routing received]:{} {}'.format(method.routing_key, body))

channel.basic_consume(
    queue=queue_name, on_message_callback=callback, auto_ack=True)

channel.start_consuming()


'''
>python python\routing_received.py info 接收单个
>python python\routing_received.py info warning  接收多个
[Routing received]:warning b'aaaa'
[Routing received]:info b'aaaa-a-a-a'
[Routing received]:info b'aaaa-a-a-a-a11313131'
[Routing received]:warning b'aaaa-a-a-a-a11313131'

'''
