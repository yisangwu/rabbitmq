# coding=utf8
'''
路由(Routing)
sender
https://www.rabbitmq.com/tutorials/tutorial-four-python.html
https://rabbitmq.mr-ping.com/tutorials_with_python/[4]Routing.html
           error
           ---- [][][][][] -> (C1)
          /
(P) -> {x}-----
         |     \
         |----- [][][][][] -> (C2)

直连交换机（direct exchange）:
交换机将会对绑定键（binding key）和路由键（routing key）进行精确匹配，从而确定消息该分发到哪个队列
'''


import sys
import pika

# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')
connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()
# 创建一个直连交换机（direct exchange）
channel.exchange_declare(exchange='direct_logs_x', exchange_type='direct')

severity = sys.argv[1] if len(sys.argv) > 1 else 'info'
message = '-'.join(sys.argv[2:]) or 'hello routing!'

channel.basic_publish(exchange='direct_logs_x',
                      routing_key=severity, body=message)
connection.close()

'''
>python python\routing_sender.py info aaaa
>python python\routing_sender.py warning  aaaa  a  a  a a11313131

'''
