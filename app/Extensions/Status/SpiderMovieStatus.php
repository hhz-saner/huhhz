<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2017/10/27
 * Time: 下午3:28
 */

namespace App\Extensions\Status;


class SpiderMovieStatus extends BaseStatus
{
    const NONE = 0;
    const TC = 1;
    const HD = 2;

    public static $defaultLabelColor = 'default';

    public static $labels = [
        self::NONE => '未知',
        self::TC => '枪版',
        self::HD => '高清',
    ];

    public static $labelColor = [
        self::NONE => 'warning',
        self::TC => 'danger',
        self::HD => 'success',
    ];

    public static function getLabelColor($def, $default = '')
    {
        return isset(static::$labelColor[$def]) ? static::$labelColor[$def] : ($default ?: static::$defaultLabelColor);
    }
}
