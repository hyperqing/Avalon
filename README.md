# Avalon
by HyperQing 2017-07-04

>“遗世独立的理想乡。”

>Workerman 基础服务

## 安装使用

1. 检测Linux环境是否符合Workerman要求
```
curl -Ss http://www.workerman.net/check.php | php
```
2. composer
```
composer install
```
3. 用到以下linux环境变量，需添加到`/etc/profile`
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

浏览器通过WebScoket连接服务器。

要求带GET参数来表示身份：

- phone: 手机
- password: 密码

## 应用层协议

### 基本格式

应用层协议使用 JSON 格式。

#### 请求格式(client->server)

WebSocket服务器能够处理各种任务，故当客户端发起请求时，应对请求的功能予以区分。

| 参数 | 类型 | 必须/可选 | 默认 | 描述 |
| ---- | ---- | ---- | ---- | ---- |
| method | string | 必须 | 无 | 请求服务器执行的方法 |
| args | object | 必须 | 默认 | 请求执行方法时传入参数 |

`method`参数取值

| 取值 | 描述 |
| ---- | ---- |
| sendMsg | 请求发送这条信息 |

例如:一对一发送消息
```json
{
  "method":"sendMsg",
  "args":{
    
  }
}
```

#### 响应格式(server->client)



## 一对一通信



## 广播

