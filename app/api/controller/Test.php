<?php
/**
 * 消息队列消费者
 * User: XuemingZhang
 * Date: 2018/6/11
 */

namespace app\api\controller;
use think\Queue\Job;
use think\Log;

class Test{

    /**
     *  测试queue方式
     */
    public function fire(Job $job, $data)
    {
        $jobInfo = json_decode($job->getRawBody(), true);
        print("<info>". var_export($jobInfo, true) ."</info>\n");
        $result = false;
        //任务执行成功
        if ($result) {
            print("<info>Hello Job has been done and deleted"."</info>\n");
            $job->delete();
        }else{
            print("<info>Job error</info>\n");
            if ($job->attempts() > 3) {
                // 超过3次错误记录到错误日志
                $job->delete();
                $this->saveLog($jobInfo, $result);
            }else{
                // 执行出错延迟3秒在执行
                $job->release(3);
            }
        }
    }

    /**
     *  测试curl方式
     */
    public function index()
    {
        echo json_encode(array('status' => 1, 'msg' => 'success', 'data' => var_export($_POST, true)));
    }

    private function saveLog($jobInfo, $resultInfo)
    {
        Log::init([
            'type'  =>  'File',
            'path'  =>  APP_PATH.'logs/mq/'
        ]);
        Log::write("日志内容：".var_export($jobInfo, true)."\r\n"."返回内容：", var_export($resultInfo, true));
    }
}
