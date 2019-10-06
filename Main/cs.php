<?php

namespace Main;

class cs
{
    use \FC\Traits\Parts;

    public function index($a = 'hello')
    {
        // 获取随机码
        $code = $this->CAPTCHA->getCode();
        $this->CAPTCHA->width = 300;
        $this->CAPTCHA->height = 100;
        $this->SESSION->set('code', $code);
        $a = $this->CACHE->setMode('file')->set('a',$code);
        die();
        $this->CAPTCHA->doImg($code);
        /*
        $this->SESSION->set('aaa',222);
        $this->COOKIES->set('aaa',333);
        $this->VIEW->assign('text', $a);
        $this->VIEW->display('index');
        */
    }

    public function index3()
    {
        $ch  = $this->CURL;
        $url = 'https://passport2-api.chaoxing.com/v11/loginregister';
        $data = '&uname=15995762831&code=tzc808809'; // 提交POST数据
        // ip参数为空会进行随机，ua为空也会进行随机
        $content = $ch->ua('widowns')->ip()->post($data)->url($url)->results('head'); // 获取内容
        print_r($ch->getCookie()); // 获取cookies，数组形式,只有设置results('head'),才会返回cookies
        print_r($content);
    }

    public function index2()
    {
        //echo $this->SESSION->get('code');
        $a = $this->CACHE->obj()->get('a');
        echo $a;
        //print_r($a);
        
    }
}
