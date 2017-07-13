<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

require_once __DIR__ . '/vendor/workerman/workerman/Autoloader.php';

// 创建一个Worker监听2345端口，使用http协议通讯
$worker = new \hyperqing\AvalonWorker("websocket://0.0.0.0:52233");

// 单进程，暂不考虑多进程通信
$worker->count = 1;
// 单进程使用的连接对象数组
$worker->useridList = [];

// 工作线程开始
$worker->onWorkerStart = function (Worker $worker) {
    // 将db实例存储在全局变量中(也可以存储在某类的静态成员中)
    global $db;
    $db = new Workerman\MySQL\Connection(getenv('MYSQL_HOST'),
        getenv('MYSQL_PORT'),
        getenv('MYSQL_USERNAME'),
        getenv('MYSQL_PASSWORD'),
        getenv('MYSQL_DBNAME')
    );
};

$worker->onConnect = function (TcpConnection $connection) {
    // 介入握手过程
    $connection->onWebSocketConnect = function (TcpConnection $connection, $http_header) {
        // TODO 过滤表单
        // 读取表单
        $phone = $_GET['phone'];
        $password = $_GET['password'];
        // 查询用户
        global $db;
        $account = $db->select('user_id,user_phone,user_password,user_name')
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

// 接收到浏览器发送的数据时回复hello world给浏览器
$worker->onMessage = function (TcpConnection $connection, $data) {
    // 测试假设消息内只有userid，则将本次消息投递到该id客户端中
    global $worker;
    $data = json_decode($data, true);
    // 如果对方上线，发给对方一份，也发给自己一份
    if (isset($worker->useridList[$data['user_id']])) {
        $worker->useridList[$data['user_id']]->send('来自 ' . $connection->user_name . ' 的信息: ' . $data['content']);
        $connection->send('我说：' . $data['content']);
    } else {
        $connection->send('对方不在线');
    }
};

Worker::runAll();
