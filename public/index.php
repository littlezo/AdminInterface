<?php

/**
 * +----------------------------------------------------------------------
 * | AdminInterface
 * +----------------------------------------------------------------------
 * | Copyright (c) 2017-2020 https://github.com/littlezo All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Author: @小小只 <soinco@qq.com>
 * +----------------------------------------------------------------------
 */

// [ 应用入口文件 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
