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

// 全局中间件定义文件
return [
    // 全局请求缓存
    \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    // \think\middleware\SessionInit::class
];
