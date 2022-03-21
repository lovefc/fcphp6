![avatar](/logo.png)

一款简单的php框架,基于php7,强大的路由分发(支持正则).
小巧方便,简单扩展、模块分离、值得在学习或者在小型项目中使用


### 安装

直接下载源码或者使用 composer 安装

composer.json
````
{
    "require": {
        "lovefc/fcphp": "6.1.9"
    }
}
````

````
composer require lovefc/fcphp:6.1.9
````

### 使用方法

````
// 报错显示
define('DEBUG', true);

// 定义错误日志
define('LOG_DIR',__DIR__.'/Log');

// 引入框架
require __DIR__ . '/vendor/autoload.php';

// 框架初始化
\FC\Main::init();

// 运行框架
\FC\Main::run();

````

### 演示案例

Main目录下包含了各种案例

|说明|位置|
|:-----  |-----  |
|基本数据库操作  |Controller/db |
|基础模型操作    |Controller/curl|
|自带的curl操作库 |Controller/curl |
|自带的验证码    |Controller/captcha|
|商品下单,并发测试   |Controller/order|
|redis操作   |Controller/redis|
|模板引擎使用   |Controller/view|








