<?php
/**
 * Created by PhpStorm.
 * User: liuzhiming
 * Date: 16-8-19
 * Time: 下午5:54
 */

namespace Lib;
use Swoole;

class WorkerServer extends Swoole\Protocol\SOAServer
{
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