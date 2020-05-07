<?php

namespace Main\Test;

use FC\Controller\BaseController;

/*
 * CURL demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 17:08:17
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 17:14:17
 */

class curl extends BaseController
{
    // 演示拉取天气接口
    public function index()
    {
        $url = 'http://t.weather.sojson.com/api/weather/city/101190101';
        $arr = $this->CURL->ua('widowns')->url($url)->results('json'); // 获取内容
        // 打印
        \FC\pre($arr);
    }
}
