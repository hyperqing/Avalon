<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

require_once __DIR__ . '/vendor/workerman/workerman/Autoloader.php';

// 创建一个Worker监听2345端口，使用http协议通讯
$worker = new Worker("websocket://0.0.0.0:52233");

// 单进程，暂不考虑多进程通信
$worker->count = 1;

// 接收到浏览器发送的数据时回复hello world给浏览器
$worker->onMessage = function (TcpConnection $connection, $data) {
    // 向浏览器发送hello world
    var_dump($data);
    $connection->send("登录成功");
};

$worker->onConnect = function (TcpConnection $connection) {
    // 读取连接ws时附带的GET参数
    $connection->onWebSocketConnect = function ($connection, $http_header) {
        var_dump($_GET);
    };
    // 向浏览器发送hello world
    $connection->send('用户' . $connection->id . '已上线');
};

Worker::runAll();
