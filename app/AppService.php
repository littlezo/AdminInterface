<?php
declare (strict_types = 1);

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


namespace app;

use think\Service;

/**
 * 应用服务类
 */
class AppService extends Service
{
    public function register()
    {
        // 服务注册
        \tauthz\TauthzService::class;
    }

    public function boot()
    {
        // 服务启动
    }
}
