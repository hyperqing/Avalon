# Avalon
by HyperQing 2017-07-04

>“遗世独立的理想乡。”

>Workerman 基础服务

## 安装使用

1. 检测 Linux 环境是否符合 Workerman 要求。
```
curl -Ss http://www.workerman.net/check.php | php
```
2. 使用 composer 安装依赖。
```
composer install
```
3. 本项目用到以下环境变量，需要添加到 Linux 的环境变量文件中`/etc/profile`。
```
export MYSQL_DBNAME=example
export MYSQL_USERNAME=example
export MYSQL_PASSWORD=example
export MYSQL_PORT=example
export MYSQL_HOST=example
export MYSQL_PREFIX=example
```

## 项目依赖

- workerman/mysql

composer require workerman/mysql

## 连接到服务器

浏览器通过 WebScoket API 连接服务器，具体见 W3C 文档 [The WebScoket API](https://www.w3.org/TR/websockets/)。

要求带GET参数来表示身份：

- phone: 手机
- password: 密码

## 应用层协议

应用层协议使用 JSON 格式。

WebScocket尽管是HTTP实现，理应有请求就有响应。但socket服务特殊，可能存在有请求无响应，或有无请求却有响应的情况出现。例如：

- 后端主动发送一条信息，此时客户端要识别并确定如何处理（忽略或弹出等）。
- 客户端要广播消息（如世界频道喊话），此时并不在意其他人是否收到。

尽管原因还有很多，但设计上会尽量使消息发送可靠。例如：

- 收到消息后，通知一下C或S端，用于接收可靠性的校验等等。

所以这里设计应用层协议时，为了确保业务可靠，会做出很多处理。

### 请求格式(client->server)

WebSocket服务器能够处理各种任务，故当客户端发起请求时，应对请求的功能予以区分。

| 参数 | 类型 | 必须/可选 | 默认 | 描述 |
| ---- | ---- | ---- | ---- | ---- |
| method | string | 必须 | 无 | 请求服务器执行的方法 |
| args | object | 必须 | 无 | 请求执行方法时传入参数 |

`method`参数取值

| 取值 | 描述 |
| ---- | ---- |
| sendMsg | 请求发送这条信息 |

`args`具体参数列表见各方法的说明。

例如:一对一发送消息
```json
{
  "method":"sendMsg",
  "args":{
    "recv_user_id":"user_id",
    "content":"消息内容"
  }
}
```

### 响应格式(server->client)

## 一对一通信

请求发送信息给对方

**方法名**
```
sendMsg
```

**参数列表**

| 参数 | 类型 | 必须/可选 | 默认 | 描述 |
| ---- | ---- | ---- | ---- | ---- |
| recv_user_id | string | 必须 | 无 | 接收对象的user_id |
| content | string | 必须 | 无 | 消息内容 |

例如：发送到
```json
{
  "method":"sendMsg",
  "args":{
    "recv_user_id":"user_id",
    "content":"消息内容"
  }
}
```


## 广播

