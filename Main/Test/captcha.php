<?php

namespace Main\Test;

/*
 * 验证码 demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 16:55:13
 * @Last Modified by: lovefc
<<<<<<< HEAD
 * @Last Modified time: 2019-10-12 17:00:21
=======
 * @Last Modified time: 2019-10-12 16:58:20
>>>>>>> 9158e1d44b2f2491000a3e7f1eb669619a1d40a5
 */

class captcha
{
    use \FC\Traits\Parts;

    // 显示验证码
    public function show()
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
