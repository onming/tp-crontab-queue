<?php
/**
 * 消息队列消费者
 * User: XuemingZhang
 * Date: 2018/6/11
 */

namespace app\queue\controller;
use think\Queue\Job;
use think\Log;

class Consumers{

    /**
     *  测试消息队列任务发送
     */
    public function fire(Job $job, $data)
    {

        $jobInfo = json_decode($job->getRawBody(), true);
        $url = $data['mq_url'];
        unset($data['mq_url']);
        $postResult = $this->curlPost($url, $data);
        $result = json_decode($postResult, true);
        //任务执行成功
        if (!empty($result['status']) && $result['status'] == 1) {
//            $this->saveLog($jobInfo, $result);
            $job->delete();
        }else{
            if ($job->attempts() > 3) {
                // 超过3次错误记录到错误日志
                $job->delete();
                $this->saveLog($jobInfo, $result);
            }else{
                // 执行出错延迟60秒在执行
                $job->release(60);
            }
        }
    }

    private function curlPost($url, $postData)
    {
        $postData = http_build_query($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        unset($postData);
        curl_close($ch);
        return $output;
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
