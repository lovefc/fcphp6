<?php

namespace Main;
use FC\Json;

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
        $a = $this->FCACHE->set('a', $code);
        $a = $this->SQLITE->table('bizhi')->limit(1)->fetch();
        print_r($a);
        //Json::result('sss','成功');
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
        $this->SESSION->clear();
    }

    public function index2()
    {
        $redLock = new \FC\Tools\RedLock();
        $r = $redLock->access('sss',10,10);
        if($r == false){
            echo '限制访问';
        }else{
            echo $r;
        }
        die();
        $lock = $redLock->lock('lovefc2', 1);
        if ($lock) {
            file_put_contents('lock.log','true'.PHP_EOL,FILE_APPEND);
            $redLock->unlock();
        } else {
           file_put_contents('lock.log','false'.PHP_EOL,FILE_APPEND);
        }


        /*
        $a = $this->REDIS->keys('w3c*');
        print_r($a);
        $a = $this->MYSQL->table('admins')->limit(1)->fetch();
        print_r($a);
        */
    }
}
