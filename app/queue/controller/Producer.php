<?php
/**
 * 消息队列生成者
 * User: XuemingZhang
 * Date: 2018/6/11
 */

namespace app\queue\controller;
use think\Exception;
use think\Queue;

class Producer{

    /**
     * 消息队列任务发送
     * @param $class    控制器方法|访问地址 app\api\command\Test | http://you_site/
     * @param $data     数据参数
     * @param $runDate  执行时间    2018-06-06 18:00:00
     * @return String
     */
    public static function addQueue($class, $data = array(), $runDate = 0)
    {
        //队列业务数据
        $queueData = ['mq_id' => uniqid(), 'mq_addtime' => time(), 'mq_adddate' => date('Y-m-d H:i:s')];
        //当前队列任务由谁处理，队列处理时默认会调用其fire方法
        if(!preg_match('/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $class)){//url请求方式
            $queueClass = $class;
        }else{
            $queueClass = 'app\queue\controller\Consumers';
            $queueData['mq_url'] = $class;
        }
        $queueData['mq_param'] = $data;
        //执行时间
        $runTime = 1;//默认1s后执行
        if($runDate){
            $runTime = strtotime($runDate) - time();
            $runTime = $runTime>0?$runTime:1;
        }else{
            $runDate = date("Y-m-d H:i:s");
        }
        //推送任务
        $queuePush = Queue::later($runTime, $queueClass, $queueData);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $queuePush !== false ){
            return "add MQ：{$class} success|run date：". $runDate;
        }else{
            return "add MQ：{$class} fail";
        }
    }

    /**
     *  测试消息队列任务发送
     */
    public static function addTest()
    {
        //当前队列任务由谁处理，队列处理时默认会调用其fire方法
        $queueClass = 'app\queue\controller\Consumers';
        //队列名称
        $queueName = 'testQueue';
        //队列业务数据
        $queueData = ['mq_id' => uniqid(), 'mq_addtime' => time(), 'mq_adddate' => date('Y-m-d H:i:s'),
            'mq_param' => [
                'task_name' => 'test task to MQ',
                'task_param_1' => 'a',
                'task_param_2' => 'b',
            ]
        ];
        //推送任务
        $queuePush = Queue::push($queueClass, $queueData, $queueName);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $queuePush !== false ){
            echo date('Y-m-d H:i:s') . " a new Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }
}
