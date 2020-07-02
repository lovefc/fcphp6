![avatar](/logo.png)

一款简单的php框架,基于php7,强大的路由分发(支持正则).
小巧方便,简单扩展、模块分离、值得在学习或者在小型项目中使用


### 安装

直接下载源码或者使用 composer 安装

composer.json
````
{
    "require": {
        "lovefc/fcphp": "6.0.7"
    }		
}
````

````
composer require lovefc/fcphp:6.0.7
````

### 使用方法

````
// 报错显示
define('DEBUG', true);

// 定义错误日志
define('LOG_DIR',__DIR__.'/Log');

// 引入框架
require dirname(__DIR__) . '/vendor/autoload.php';

// 框架初始化
\FC\Main::init();

// 运行框架
\FC\Main::run();

````

### 交流讨论

扣扣群号：572905973

文档链接: https://www.kancloud.cn/lovefc/fckj




