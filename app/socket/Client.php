<?php

namespace app\WebSocket;

use \GatewayClient\Gateway;

/**
 *====这个步骤是必须的====
 *这里填写Register服务的ip和Register端口，注意端口不是gateway端口
 *ip不能是0.0.0.0，端口在start_register.php中可以找到
 *这里假设GatewayClient和Register服务都在一台服务器上，ip填写127.0.0.1。
 *如果不在一台服务器则填写真实的Register服务的内网ip(或者外网ip)
 **/
Gateway::$registerAddress = '127.0.0.1:1236';

class Client
{


    public static function sendToAll ($data)
    {
        return Gateway::sendToAll($data);
    }
    public static function sendToClient ($client_id, $data)
    {
        return Gateway::sendToClient($client_id, $data);
    }
    public static function closeClient($client_id)
    {
        return Gateway::closeClient($client_id);
    }
    public static function isOnline($client_id)
    {
        return Gateway::isOnline($client_id);
    }
    public static function bindUid($client_id, $uid)
    {
        return  Gateway::bindUid($client_id, $uid);
    }
    public static function isUidOnline($uid)
    {
        return Gateway::isUidOnline($uid);
    }
    public static function getClientIdByUid($client_id)
    {
        return Gateway::getClientIdByUid($client_id);
    }
    public static function unbindUid($client_id, $uid)
    {
        return Gateway::unbindUid($client_id, $uid);
    }
    public static function sendToUid($uid, $data)
    {
        return Gateway::sendToUid($uid, $data);
    }
    public static function joinGroup($client_id, $group)
    {
        return Gateway::joinGroup($client_id, $group);
    }
    public static function sendToGroup($group, $data)
    {
        return Gateway::sendToGroup($group, $data);
    }
    public static function leaveGroup($client_id, $group)
    {
        return Gateway::leaveGroup($client_id, $group);
    }
    public static function getClientCountByGroup($group)
    {
        return Gateway::getClientCountByGroup($group);
    }
    public static function getClientSessionsByGroup($group)
    {
        return Gateway::getClientSessionsByGroup($group);
    }
    public static function getAllClientCount ()
    {
        return Gateway::getAllClientCount();
    }
    public static function getAllClientSessions ()
    {
        return Gateway::getAllClientSessions();
    }
    public static function setSession($client_id, $session)
    {
        return Gateway::setSession($client_id, $session);
    }
    public static function updateSession($client_id, $session)
    {
        return Gateway::updateSession($client_id, $session);
    }
    public static function getSession($client_id)
    {
        return Gateway::getSession($client_id);
    }
}