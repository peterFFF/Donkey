<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午3:56
 */

namespace Lib;
use Swoole;

class CentreServer  extends Swoole\Protocol\SOAServer
{
    function onWorkerStart($server, $worker_id)
    {
        if (!$server->taskworker){
            if ($worker_id == 0 ){
                //echo "开始计时:".date("Y-m-d H:i:s")."\n";
                $server->after((60-date("s"))*1000,function () use ($server){
                    $server->task("load");
                    //echo "开始after计时:".date("Y-m-d H:i:s")."\n";
                    $server->tick(60000, function () use ($server) {
                        //echo "开始task:".date("Y-m-d H:i:s")."\n";
                        $server->task("load");
                    });
                });
            }
            if ($worker_id == 1){
                $server->tick(1000, function () use ($server) {
                    echo "开始tick计时:".date("Y-m-d H:i:s")."\n";
                    $tasks = Tasks::getTasks();
                    $server->task($tasks);
                });
            }
        }
    }
    function onTask($serv, $task_id, $from_id, $data)
    {
        if ($data == "load"){
            Tasks::checkTasks();
        }else{

        }

        return true;
    }
    function onFinish($serv, $task_id, $data)
    {
        return;
    }
    public function call($request, $header)
    {
        //初始化日志
        Flog::startLog($request['call']);
        Flog::log("call:".$request['call'].",params:".json_encode($request['params']));
        $ret =  parent::call($request, $header); // TODO: Change the autogenerated stub
        Flog::log($ret);
        Flog::endLog();
        Flog::flush();
        return $ret;
    }
}