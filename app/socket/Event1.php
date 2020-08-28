<?php

namespace app\WebSocket\Event;

use \GatewayWorker\Lib\Gateway;
use Workerman\Worker;
class Event
{
    public static function onWorkerStart($businessWorker)
    {
       echo "WorkerStart\n";
    }
    public static function onConnect($client_id)
    {
       Gateway::sendToCurrentClient("Your client_id is $client_id");
    }
    public static function onWebSocketConnect($client_id, $data)
    {
        var_export($data);
        if (!isset($data['get']['token'])) {
             Gateway::closeClient($client_id);
        }
    }
    /**
     * 有消息时触发该方法
     * @param int $client_id 发消息的client_id
     * @param mixed $message 消息
     * @return void
     */
    public static function onMessage($client_id, $message)
    {
        // 群聊，转发请求给其它所有的客户端
        $data = json_decode($message, true);
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
                $isBuid = Gateway::getUidByClientId($client_id);
                if ($isBuid->isEmpty()) {
                    $message =  [
                        'event'      => 'login',
                        'client_id' => $client_id,
                    ];
                    Gateway::sendToClient($client_id, json_encode($message, true));
                } else {
                    $message =  [
                        'event'      => 'PONG',
                    ];
                    Gateway::sendToUid($client_id, json_encode($message, true));
                }
                break;
            default:
        }
    }
    /**
     * 当用户断开连接时触发的方法
     * @param integer $client_id 断开连接的客户端client_id
     * @return void
     */
    public static function onClose($client_id)
    {
       // 广播 xxx logout
       GateWay::sendToAll("client[$client_id] logout\n");
    }
    public static function onWorkerStop($businessWorker)
    {
       echo "WorkerStop\n";
    }

}