<?php

namespace FC\Server;

/*
 * @Author: lovefc 
 * @Date: 2019-09-05 17:28:47 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-05 17:30:02
 */

class Request
{
  public $server = [];

  static private $instance;

  private function __construct()
  {
    $this->init();
  }

  //防止克隆对象
  private function __clone()
  { }

  public static function getInstance()
  {
    if (!self::$instance) {
      $c = __CLASS__;
      self::$instance = new $c;
    }
    return self::$instance;
  }

  // 设置
  public function __set($k, $c)
  {
    $k =  strtoupper($k); // 转成大写
    $this->server[$k] = $c;
  }

  // 获取
  public function __get($k)
  {
    $k =    strtoupper($k); // 转成大写
    return $this->read($k);
  }

  // 判断是否为ajax请求
  public function isAjax(): int
  {
    $is_ajax = (($_POST['IS_AJAX'] ?? 0) || ($_GET['IS_AJAX'] ?? 0)) ? 1 : 0;

    $ajaxStatus = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 0 && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || $is_ajax) ? 1 : 0;

    return $ajaxStatus;
  }

  // 初始化
  public function init()
  {
    $this->fc_stime = microtime(true);
    $this->is_ajax = $this->isAjax();
  }

  // 读取 
  public function read($k)
  {
    $val = $_SERVER[$k] ?? '';
    $this->server[$k] = $val;
    return $val;
  }
}
