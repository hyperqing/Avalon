<?php

namespace hyperqing;

use Workerman\Connection\TcpConnection;

class AvalonTcpConnection extends TcpConnection
{
    /**
     * 用户ID
     * @var string
     */
    public $user_id;
    /**
     * 用户名
     * @var string
     */
    public $user_name;
    /**
     * 用户手机号
     * @var string
     */
    public $user_phone;

    /**
     * WebSocket握手过程
     * @var callable
     */
    public $onWebSocketConnect;
}
