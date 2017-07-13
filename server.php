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

/**
 * 连接握手过程
 * @param TcpConnection $connection
 */
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
            // 检查对方是否在线
            if (isset($worker->useridList[$data['recv_user_id']])) {
                $recv_user = $worker->useridList[$data['recv_user_id']];
                // 封装返回消息
                $ret = [
                ];
                $recv_user->send(json_encode($ret));
            } else {
                // TODO 对方不在线的情况，这里应将消息存入数据库，待对方上线后获取
                // 封装返回消息给自己
                $connection->send('对方不在线');
            }
            break;
    }
};

/**
 * 用户下线处理
 *
 * 及时释放空间，对于非极端情况下线，可以正常处理
 * @param TcpConnection $connection
 */
$worker->onClose = function (TcpConnection $connection) {
    // TODO 后期结合长连接心跳检测实现
    if (isset($connection->user_id)) {
        global $worker;
        // 从用户列表中释放
        unset($worker->useridList[$connection->user_id]);
    }
};

Worker::runAll();
