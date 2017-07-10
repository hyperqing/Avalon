<?php

namespace hyperqing;

use Workerman\Worker;

/**
 * Class AvalonWorker
 * @package hyperqing
 * @author HyperQing<469379004@qq.com>
 */
class AvalonWorker extends Worker
{
    /**
     * 用户连接数组
     *
     * (仅供单进程时使用)
     * @var array
     */
    public $useridList = [];
}