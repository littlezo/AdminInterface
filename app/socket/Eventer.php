<?php
/**
 * +----------------------------------------------------------------------
 * | SolCo
 * +----------------------------------------------------------------------
 * | Copyright (c) 2017-2020 http://esoin.cc All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Author: @小小只 <soinco@qq.com>
 * +----------------------------------------------------------------------
 */
namespace app\socket;

use \GatewayWorker\Lib\Gateway;
use \Workerman\Worker;

/**
 * WebSocket 服务类
 */
class Eventer
{
    /**
     * onWorkerStart 事件回调
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次
     *
     * @access public
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStart(Worker $businessWorker)
    {
        $app = new \think\worker\Application;
        $app->initialize();
    }

    /**
     * onConnect 事件回调
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发
     *
     * @access public
     * @param  int       $client_id
     * @return void
     */
    public static function onConnect($client_id)
    {
        $message =  [
            'event'      => 'login',
            'client_id' => $client_id,
        ];
        Gateway::sendToCurrentClient(json_encode($message, true));
    }

    /**
     * onWebSocketConnect 事件回调
     * 当客户端连接上gateway完成websocket握手时触发
     *
     * @param  integer  $client_id 断开连接的客户端client_id
     * @param  mixed    $data
     * @return void
     */
    public static function onWebSocketConnect($client_id, $data)
    {
        var_export($data);
    }

    /**
     * onMessage 事件回调
     * 当客户端发来数据(Gateway进程收到数据)后触发
     *
     * @access public
     * @param  int       $client_id
     * @param  mixed     $data
     * @return void
     */
    public static function onMessage($client_id, $data)
    {
        $data = json_decode($data, true);
        $event = $data['event'];
        switch ($event) {
            case 'bind': // 登陆
                if ($data['group'] && $data['uuid']) { // 绑定及加入群组
                    Gateway::bindUid($client_id, $data['uuid']);
                    Gateway::joinGroup($client_id, $data['group']);
                    $message =  [
                        'event'      => 'online',
                        'client_id' => $client_id,
                        'msg' => "用户上线并加入了群组",
                    ];
                } elseif ($data['uuid']) { // 绑定
                    Gateway::bindUid($client_id, $data['uuid']);
                    $message =  [
                        'event'      => 'online',
                        'client_id' => $client_id,
                        'msg' => "用户上线了",
                    ];
                } elseif ($data['group']) { // 加入群组
                    Gateway::joinGroup($client_id, $data['group']);
                    $message =  [
                        'event'      => 'joinGroup',
                        'client_id' => $client_id,
                        'msg' => "用户加入群组",
                    ];
                }
                Gateway::sendToClient($client_id, json_encode($message, true));
                break;
            case 'join': // 加入群组
                Gateway::joinGroup($client_id,  $data['group']);
                $message =  [
                    'event'      => 'joinGroup',
                    'msg' => "用户加入群组",
                ];
                Gateway::sendToClient($client_id, json_encode($message, true));
                break;
            case 'PING': // 心跳包
                // $isBuid = Gateway::getUidByClientId($client_id);
                // if ($isBuid->isEmpty()) {
                //     $message =  [
                //         'event'      => 'login',
                //         'client_id' => $client_id,
                //     ];
                //     Gateway::sendToClient($client_id, json_encode($message, true));
                // } else {
                    $message =  [
                        'event'      => 'PONG',
                    ];
                    Gateway::sendToUid($client_id, json_encode($message, true));
                // }
                break;
            default:
        }
    }

    /**
     * onClose 事件回调 当用户断开连接时触发的方法
     *
     * @param  integer $client_id 断开连接的客户端client_id
     * @return void
     */
    public static function onClose($client_id)
    {
        $broadcast =  [
            'event' => 'null',
            'data'      => "用户[$client_id]下线了！",
        ];
        GateWay::sendToAll(json_encode($broadcast, true));
    }

    /**
     * onWorkerStop 事件回调
     * 当businessWorker进程退出时触发。每个进程生命周期内都只会触发一次。
     *
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStop(Worker $businessWorker)
    {
        echo "WorkerStop\n";
    }
}
