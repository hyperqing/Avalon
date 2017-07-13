<?php
require_once __DIR__ . '/vendor/autoload.php';

use hyperqing\AvalonWorker;
use hyperqing\Db;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

require_once __DIR__ . '/vendor/workerman/workerman/Autoloader.php';

// 创建一个Worker监听端口
$worker = new AvalonWorker("websocket://0.0.0.0:52233");

// 单进程，暂不考虑多进程通信
$worker->count = 1;

// 工作线程开始
$worker->onWorkerStart = function (Worker $worker) {
};

// 连接过程处理
$worker->onConnect = function (TcpConnection $connection) {
    // 介入握手过程
    $connection->onWebSocketConnect = function (TcpConnection $connection, $http_header) {
        // TODO 过滤表单
        // 读取表单
        $phone = $_GET['phone'];
        $password = $_GET['password'];
        // 查询用户
        $account = Db::instance()->select('user_id,user_phone,user_password,user_name')
            ->from('oc_account')
            ->where('user_phone= :phone')
            ->bindValues(['phone' => $phone])
            ->row();
        if (!$account) {
            $connection->send('用户不存在');
            $connection->close();
            return;
        }
        if (!\hyperqing\Password::verify($password, $account['user_password'])) {
            $connection->send('手机或密码错误');
            $connection->close();
            return;
        }
        // 登录成功的情况，绑定用户id
        $connection->user_id = $account['user_id'];
        $connection->user_phone = $account['user_phone'];
        $connection->user_name = $account['user_name'];
        // 存入连接对象数组，方便使用
        global $worker;
        $worker->useridList[$connection->user_id] = $connection;
    };
    // 此处不添加代码，按照回调执行顺序，先执行$worker->onConnect，然后$connection->onWebSocketConnect。
};

/**
 * 监听消息
 * @param TcpConnection $connection
 * @param $data
 */
$worker->onMessage = function (TcpConnection $connection, $data) {
    global $worker;
    $json = json_decode($data, true);
    // 按请求的功能进行处理
    switch ($json['method']) {
        case 'sendMsg':
            break;
    }

    if (isset($worker->useridList[$data['user_id']])) {
        $worker->useridList[$data['user_id']]->send('来自 ' . $connection->user_name . ' 的信息: ' . $data['q']);
        $connection->send('我说：' . $data['content']);
    } else {
        $connection->send('对方不在线');
    }
};

Worker::runAll();
