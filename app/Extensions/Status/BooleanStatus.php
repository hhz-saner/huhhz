<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2017/10/27
 * Time: 下午3:28
 */

namespace App\Extensions\Status;


class BooleanStatus extends BaseStatus
{
    const FALSE = 0;
    const TRUE = 1;

    public static $labels = [
        self::FALSE => '否',
        self::TRUE => '是',
    ];
}
