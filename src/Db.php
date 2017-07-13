<?php

namespace hyperqing;

use Workerman\MySQL\Connection;

class Db
{
    /**
     * workerman/mysql实例
     * @var \Workerman\MySQL\Connection
     */
    private static $db;

    public static function instance()
    {
        if (self::$db == null) {
            self::$db = new Connection(getenv('MYSQL_HOST'),
                getenv('MYSQL_PORT'),
                getenv('MYSQL_USERNAME'),
                getenv('MYSQL_PASSWORD'),
                getenv('MYSQL_DBNAME')
            );
        }
        return self::$db;
    }
}
