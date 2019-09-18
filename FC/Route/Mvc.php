<?php

namespace FC\Route;

use FC\Load\LoaderClass;

use FC\Route AS LuYou;

/*
 * 模拟MVC
 * @Author: lovefc 
 * @Date: 2019-09-17 08:24:01 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-18 15:35:02
 */

class MVC
{
    public $mvc_dir; // mvc目录

    public $style_name; // 模版名称

    public $style_cookie_name; // 模版cookies名称

    public $style_html_dir; // 风格html目录

    public $style_template_dir; // 模版目录

    public $style_template_suffix; // 模版后缀

    public $style_template_tempdir; // 模版编译目录


    /**
     * 初始化函数
     */
    public function run()
    {
        $this->style_html_dir = rtrim($this->style_html_dir, '/'); //去掉后面的/,兼容处理

        $obj = GetObj('fcphp\start\tplStart');

        $obj->setsuffix($this->style_template_suffix); //设置模版后缀

        $ck_name = Input($this->style_cookie_name, $_COOKIE);
        !$ck_name && $ck_name = $this->style_name;
        if (is_dir($this->style_template_dir . $ck_name)) {
            $obj->setdirs($this->style_template_dir . $ck_name)->settempdirs($this->style_template_tempdir . $ck_name);
        }
        /**  以下是设置要用到的常量 **/
        if (strstr($this->style_html_dir, 'http')) {
            define('STATIC_DIR', $this->style_html_dir);
            define('STATIC_RT_DIR', $this->style_html_dir);
            define('HTTP_STATIC_DIR', $this->style_html_dir);
        } else {
            GetRootDir($this->style_html_dir, 'STATIC_DIR'); //静态文件的主目录
            GetRootDir($this->style_html_dir, 'STATIC_RT_DIR', false); //静态文件的相对路径
            GetRootDir($this->style_html_dir, 'HTTP_STATIC_DIR', true, true); //静态文件的主目录
        }

        /** 以下是模拟路由  **/

        //实例化路由初始化类
        $obj = GetObj('routeStart', 'start');
        //定义路由中的分隔符
        $obj::$cutting = '/';

        //加载控制器
        LoaderClass::AddPsr4('controller', $this->mvc_dir . 'controller');

        //加载model
        LoaderClass::AddPsr4('model', $this->mvc_dir . 'model');

        $Index = false;
        $url = $obj::get_query();
        $m = explode($obj::$cutting, $url);
        if (count($m) >= 1) {
            $Index = $m[0];
        }
        if (!empty($Index)) {
            if (is_file($this->mvc_dir . 'controller/' . $Index . '.php')) {
                if (!isset($obj::$routeval[$Index]))
                    $obj->set($Index, '\\controller\\' . $Index);
            }
        }
        $obj::run();
    }

    //初始化设置
    public function init()
    {

        //MVC目录
        $this->mvc_dir = empty($this->mvc_dir) ? SPATH . '/application/' : rtrim($this->mvc_dir, '/') . '/';

        //要获取的cookie名
        $this->style_cookie_name = empty($this->style_cookie_name) ? md5(APP_NAME . 'styles') : $this->style_cookie_name;

        if (empty($this->style_html_dir)) {
            $this->style_html_dir = $this->mvc_dir . 'static/';
        }

        $this->style_template_dir = empty($this->style_template_dir) ? $this->mvc_dir . 'template/' : rtrim($this->style_template_dir, '/') . '/';

        $this->style_template_tempdir = empty($this->style_template_tempdir) ? $this->mvc_dir . 'runtime/' : rtrim($this->style_template_tempdir, '/') . '/'; //模版缓存目录
    }
}
