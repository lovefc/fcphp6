<?php

namespace Main\Test;

/*
 * CURL demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 17:08:17
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 17:14:17
 */

class curl
{
    use \FC\Traits\Parts;

    // 演示拉取图片接口
    public function pic()
    {
        $url = 'https://www.apiopen.top/meituApi?page=1';
        $arr = $this->CURL->ua('widowns')->url($url)->results('json'); // 获取内容
        // 打印
        \FC\pre($arr);
    }
}
