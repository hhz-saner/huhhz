<?php
/**
 * Created by PhpStorm.
 * User: saner
 * Date: 2019/4/16
 * Time: 10:43 AM
 */

namespace App\Helper;

class Helper
{
    public static function isWechat()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}
