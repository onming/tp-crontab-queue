# tp-crontab-queue
thinkphp框架 crontab定时脚本+queue结合应用


# queue配置
> app/extra/queue.php 消息队列配置文件地址
```
return [
    'connector'  => 'Redis',          // Redis 驱动
    'expire'     => 60,               // 任务的过期时间，默认为60秒; 若要禁用，则设置为 null
    'default'    => 'myQueue',  // 默认的队列名称
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
> app/command.php 自定义命令增加
```
return [
    'app\api\command\Crontab',
];
```
> app/command/Crontab.php 定时脚本配置文件地址
定时时间以linux格式编写
```
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
```
//queue方式
$output->writeln(self::addTask('app\api\controller\Test', array('param_1' => 1, 'param_2' => 2), "*/1 * * * *"));
//curl方式
$output->writeln(self::addTask('http://you_site/api/test/index', array('param_1' => 1, 'param_2' => 2), "*/5 * * * *"));

```

# crontab使用方式
```
$php /home/wwwroot/tp-crontab-queue/think crontab

$add MQ：app\api\controller\Test success|run date：2018-06-11 17:35:26
$add MQ：http://onming.cn/api/test/index success|run date：2018-06-11 17:35:26

```
可以添加到linux的定时脚本里面

# queue命令模式
```
#启动命令
$php /home/wwwroot/tp-crontab-queue/think queue:work --queue myQueue
#关闭命令
$php /home/wwwroot/tp-crontab-queue/think queue:restart
```
Work模式
```
php think queue:work
--daemon                //是否循环执行，如果不加该参数，则该命令处理完下一个消息就退出
--queue  helloJobQueue  //要处理的队列的名称
--delay  0              //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
--force                 //系统处于维护状态时是否仍然处理任务，并未找到相关说明
--memory 128            //该进程允许使用的内存上限，以 M 为单位
--sleep  3              //如果队列中无任务，则sleep多少秒后重新检查(work+daemon模式)或者退出(listen或非daemon模式)
--tries  2              //如果任务已经超过尝试次数上限，则触发‘任务尝试次数超限’事件，默认为0
```

Listen模式
```
php think queue:listen
--queue  helloJobQueue   //监听的队列的名称
--delay  0               //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
--memory 128             //该进程允许使用的内存上限，以 M 为单位
--sleep  3               //如果队列中无任务，则多长时间后重新检查，daemon模式下有效
--tries  0               //如果任务已经超过重发次数上限，则进入失败处理逻辑，默认为0
--timeout 60             //创建的work子进程的允许执行的最长时间，以秒为单位
```
