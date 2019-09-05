<?php

/*
 * @Author: lovefc 
 * @Date: 2019-09-05 17:28:47 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-05 17:30:02
 */

class Request
{
  public $server = [];
  
  
  public function __construct(){
        $this->init();
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


class cs{
    public $server;
    public function __construct(Request $Request){
        $this->server = $Request::getInstance();
    }  
    public function demo(){
        echo '1111';   
    }
}


class Container
{
  public $s = array();
  
  // 创建值
  public function __set($k, $c)
  {
    $this->s[$k] = $c;
  }
  // 获取值
  public function __get($k)
  {
    return $this->build($this->s[$k]);
  }
  /**
   * 自动绑定（Autowiring）自动解析（Automatic Resolution）
   *
   * @param string $className
   * @return object
   * @throws Exception
   */
  public function build($className)
  {
    // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
    if ($className instanceof Closure) {
      // 执行闭包函数，并将结果返回
      return $className($this);
    }
    
    /*通过反射获取类的内部结构，实例化类*/
    $reflector = new ReflectionClass($className);
    // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
    if (!$reflector->isInstantiable()) {
      throw new Exception("Can't instantiate this.");
    }
    
    /** @var ReflectionMethod $constructor 获取类的构造函数 */
    $constructor = $reflector->getConstructor();
    // 若无构造函数，直接实例化并返回
    if (is_null($constructor)) {
      return new $className;
    }
    // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
    $parameters = $constructor->getParameters();
    
    // 递归解析构造函数的参数
    
    $dependencies = $this->getDependencies($parameters);
    
    // 创建一个类的新实例，给出的参数将传递到类的构造函数。
    
    return $reflector->newInstanceArgs($dependencies);
  }
  /**
   * @param array $parameters
   * @return array
   * @throws Exception
   */
  public function getDependencies($parameters)
  {
    $dependencies = [];
    /** @var ReflectionParameter $parameter */
    foreach ($parameters as $parameter) {
      /** @var ReflectionClass $dependency */
      $dependency = $parameter->getClass();
      if (is_null($dependency)) {
        // 是变量,有默认值则设置默认值
        $dependencies[] = $this->resolveNonClass($parameter);
      } else {
        // 是一个类，递归解析
        $dependencies[] = $this->build($dependency->name);
      }
    }
    return $dependencies;
  }
  /**
   * @param ReflectionParameter $parameter
   * @return mixed
   * @throws Exception
   */
  public function resolveNonClass($parameter)
  {
    // 有默认值则返回默认值
    if ($parameter->isDefaultValueAvailable()) {
      return $parameter->getDefaultValue();
    }
    throw new Exception('I have no idea what to do here.');
  }
}

$c = new Container();

$c->Request = 'Request';

print_r($c->Request->is_ajax);

