<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2017/10/27
 * Time: 下午3:28
 */

namespace App\Extensions\Status;


class Status extends BaseStatus
{
    const SUCCESS = 200;
    const FAILED = 400;
    const UNAUTHORIZED = 403;
    const NOT_FOUND = 404;

    public static $labels = [
        self::SUCCESS => '成功',
        self::FAILED => '失败',
        self::UNAUTHORIZED => '未登录',
        self::NOT_FOUND => '未找到',
    ];
}