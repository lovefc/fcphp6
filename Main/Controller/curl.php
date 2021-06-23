<?php

namespace Main\Controller;

use FC\Controller\BaseController;

/*
 * CURL demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 17:08:17
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 09:23:17
 */

class curl extends BaseController
{
    // 演示拉取天气接口
    public function index()
    {
        $url = 'http://www.weather.com.cn/data/sk/101010100.html';
        $arr = $this->CURL->ua('widowns')->url($url)->results('json'); // 获取内容
        // 打印
        \FC\pre($arr);
    }
}
