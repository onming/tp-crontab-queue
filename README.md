# tp-crontab-queue
thinkphp框架 crontab定时脚本+queue结合应用


# queue配置
app/extra/queue.php

```
return [
    'connector'  => 'Redis',          // Redis 驱动
    'expire'     => 60,               // 任务的过期时间，默认为60秒; 若要禁用，则设置为 null
    'default'    => 'scfc_by_queue',  // 默认的队列名称
    'host'       => '127.0.0.1',      // redis 主机ip
    'port'       => 6379,             // redis 端口
    'password'   => '',               // redis 密码
    'select'     => 8,                // 使用哪一个 db，默认为 db0
    'timeout'    => 0,                // redis连接的超时时间
    'persistent' => false,            // 是否是长连接

//    'connector'   => 'Sync',		    // Sync 驱动，该驱动的实际作用是取消消息队列，还原为同步执行
];
```

# crontab配置

定时时间以linux格式编写
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
```
//queue方式
$output->writeln(self::addTask('app\api\controller\Test', array('param_1' => 1, 'param_2' => 2), "*/1 * * * *"));
//curl方式
$output->writeln(self::addTask('http://you_site/api/test/index', array('param_1' => 1, 'param_2' => 2), "*/5 * * * *"));

```

