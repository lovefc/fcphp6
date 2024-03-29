<?php

namespace Main\Controller;

use FC\Controller\BaseController;

/*
 * 验证码 demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 16:55:13
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 11:08:37
 */

class captcha extends BaseController
{
    // 显示验证码
    public function index()
    {
        // 获取验证码字符串
        $code = $this->CAPTCHA->getCode();
        // 验证码宽度
        $this->CAPTCHA->width = 300;
        // 验证码高度
        $this->CAPTCHA->height = 100;
        // 将验证码存到session
        $this->SESSION->set('code', $code);
        // 显示图片
        $this->CAPTCHA->doImg($code);
    }

    // 读取code的值
    public function code()
    {
        echo $this->SESSION->get('code');
    }
}
