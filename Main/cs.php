<?php

namespace Main;

class cs
{
    use \FC\Traits\Parts;

    public function index($a = 'hello')
    {
        // 获取随机码
        $code = $this->CAPTCHA->getCode();
        $this->SESSION->set('code',$code);
        $this->CAPTCHA->doImg($code);
        /*
        $this->SESSION->set('aaa',222);
        $this->COOKIES->set('aaa',333);
        $this->VIEW->assign('text', $a);
        $this->VIEW->display('index');
        */
    }

    public function index2()
    {
        echo $this->SESSION->get('code');
    }
}
