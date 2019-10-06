<?php

/*
 * 事件设定
 * @Author: lovefc 
 * @Date: 2019-09-20 14:37:43 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-07 00:19:29
 */

return [

  // 页面开始的触发事件
  'OnLoad' => [function () {
    $v = $_GET['fc'] ?? '';
    if ($v) {
      echo 'hi，我是封尘！你好像触发了什么。。。。'.FC_EOL;
    }
  }],
  
  // 监听错误发生时要处理的事件函数，参数是
  'ListError' => [],  

  // 路由访问前的设置,可以来设置一些权限等等
  'Route'     =>[function($route){
    if($route == '#^html/([0-9]*).html(.*)$#'){
       echo '你当前访问的路由是 '.$route.FC_EOL;
    }
  }],

];
