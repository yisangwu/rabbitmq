# coding=utf8
"""
rabbitmq sender
https://github.com/rabbitmq/rabbitmq-tutorials
https://www.rabbitmq.com/tutorials/tutorial-one-python.html

        hello
(P) ->[][][][][] -> (C)

 # rabbitmqctl list_queues
"""

import pika

# 认证信息
credentials = pika.PlainCredentials('admin', 'admin')

# 建立一个到RabbitMQ服务器的连接
connection = pika.BlockingConnection(
    pika.ConnectionParameters(host='192.168.50.83', credentials=credentials))

channel = connection.channel()

# 创建一个名为"hello-py"的队列用来将消息投递进去
channel.queue_declare(queue='hello-py')

# exchange 交换机， 空字符串代表默认或者匿名交换机
# routing_key 队列名字
channel.basic_publish(exchange='', routing_key='hello-py',
                      body='Hello World Python!')

channel.close()

# 在退出程序之前，我们需要确认网络缓冲已经被刷写、消息已经投递到RabbitMQ。通过安全关闭连接可以做到这一点。
connection.close()

# http://www.rabbitmq.com/configure.html#config-items
# 如果这是你第一次使用RabbitMQ，并且没有看到“Sent”消息出现在屏幕上，你可能会抓耳挠腮不知所以。
# 这也许是因为没有足够的磁盘空间给代理使用所造成的（代理默认需要200MB的空闲空间），所以它才会拒绝接收消息。
# 查看一下代理的日志文件进行确认，如果需要的话也可以减少限制。配置文件文档会告诉你如何更改磁盘空间限制（disk_free_limit）。
