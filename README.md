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

## 一对一通信

## 广播

