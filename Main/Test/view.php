<?php

namespace Main\Test;

use FC\Controller\BaseController;

/*
 * template 操作类
 * @Author: lovefc 
 * @Date: 2019-10-13 09:43:21 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-13 09:46:07
 */

class view extends BaseController
{
    // 打印信息
    public function index($arr)
    {
        $arr = empty($arr) ? ['我','爱','你','封','尘'] : [];
        $this->VIEW->assign('res', $arr);
        $this->VIEW->display('index');
    }
}
