<?php

namespace Main\Test;

/*
 * template 操作类
 * @Author: lovefc 
 * @Date: 2019-10-13 09:43:21 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-13 09:46:07
 */

class view
{
    use \FC\Traits\Parts;

    // 打印信息
    public function index()
    {
        $text = '我爱你封尘';
        $this->VIEW->assign('text', $text);
        $this->VIEW->display('index');
    }
}
