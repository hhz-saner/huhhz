<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2017/10/25
 * Time: 下午2:36
 */

namespace App\Extensions\Status;


abstract class BaseStatus
{
    public static $labels = [];

    public static $defaultLabel = '';

    public static function getLabel($def, $default = '')
    {
        return isset(static::$labels[$def]) ? static::$labels[$def] : ($default ?: static::$defaultLabel);
    }

    public static function getLabels($withAll = false, $allLabel = '全部')
    {
        $labels = static::$labels;
        if ($withAll) {
            $labels = array_merge(['' => $allLabel], $labels);
        }

        return $labels;
    }
}