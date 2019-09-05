<?php

/**
 * 框架基本入口
 * author:lovefc
 */

//记录一下时间
$_SERVER['FC_STIME'] = microtime(true);

//记录一下现在的内存
function_exists('memory_get_usage') && $_SERVER['start_memory'] = memory_get_usage();

//回退页面,指定会话页面所使用的缓冲控制方法
//defined('SESSION_CACHE') &&  session_cache_limiter('private, must-revalidate');

// 是否自动打开session
ini_set('session.auto_start', 1);

//获取框架所在的根目录，可以自己设定
defined('PATH') || define("PATH", strtr(dirname(__DIR__), '\\', '/'));

//获取当前项目访问的根路径
define("SPATH", dirname($_SERVER['SCRIPT_FILENAME']));

/**  兼容操作,用来兼容一些环境 **/
//pathinfo下对于PHP_SELF的兼容，避免出现不必要的值和安全问题,如果要获取本页面地址，推荐使用$_SERVER['SCRIPT_NAME']
if (isset($_SERVER['PATH_INFO'])) {
    //$phpfile = basename($_SERVER['SCRIPT_FILENAME']);//取得当前访问文件的物理路径
    //$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], $phpfile)).$phpfile;
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}

//开启别名的情况下，对根目录的重写，不然为默认的根目录,这是一个兼容的方案
if (defined('MODEL_ALIAS')) {
    $sdroot = SPATH . '/';//取得当前访问的物理目录
    $droot = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1);//取得访问的根目录
    $_SERVER['DOCUMENT_ROOT'] = str_replace($droot, '', $sdroot) . '/';
}

//检测是否定义fastcgi_finish_request
if (!function_exists("fastcgi_finish_request")) {
    function fastcgi_finish_request()
    {
    }
}

//判断get_magic_quotes_gpc
if (function_exists('get_magic_quotes_gpc')) {
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
} else {
    define('MAGIC_QUOTES_GPC', 0);
}
/**  兼容操作 end **/


//定义是否为cli操作
if (PHP_SAPI === 'cli') {
    define('IS_CLI', true);
    set_time_limit(0);
} else {
    define('IS_CLI', false);
}

//定义时区
!defined('TIMEZONE') ? date_default_timezone_set('PRC') : date_default_timezone_set(TIMEZONE);

//定义编码
!defined('CHARSET') ? header("Content-type:text/html; charset=utf-8") : header('Content-type: text/html; charset=' . CHARSET);

//判断版本
version_compare(PHP_VERSION, '5.5.0', '<') && exit("fcphp框架只能运行在php5.6版本或以上的环境中,敬请见谅!\n");

//判断错误是否打开
$debug = defined('DEBUG') ? DEBUG : true;
if ($debug != false) {
    if (PHP_SAPI === 'cli') {
        ini_set('display_errors', 'Off');
        error_reporting(0);
    } else {
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
    }
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

//对，我就说我是5.0,反正也没人用
define('FC_VERSION', '5.0');

//http:// or https://
$schema = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';

defined('HTTP_SCHEMA') || define('HTTP_SCHEMA', $schema);

//检测当前的端口
define("SERVER_PORT", isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');

//定义当前的域名
define("SERVER_NAME", isset($_SERVER['SERVER_NAME']) ? trim($_SERVER['SERVER_NAME'], '/') : '');

//获取服务器的ip
define('SERVER_IP', isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');

//定于当前的域名,带端口的
define("HTTP_HOST", isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));

//定义框架所在目录
define("FC_DIR", strtr(__DIR__, '\\', '/'));

//获取时间
define('TIME', time());

//检测是否有POST数据
define('IS_POST', isset($_POST) && count($_POST) > 0 ? true : false);

//判断ajax的条件
$is_ajax = (isset($_POST['IS_AJAX']) && $_POST['IS_AJAX'] == 1) || (isset($_GET['IS_AJAX']) && $_GET['IS_AJAX'] == 1) ? true : false;
unset($_POST['IS_AJAX']);

//是否为AJAX请求
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $is_ajax == true ? true : false);

//请求方法
define('QUERY_METHOD', isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '');

//自动加载类
require(FC_DIR . '/core/LoaderClass.php');

//默认函数库
require(FC_DIR . '/function/function.php');

//条件判断函数库
require(FC_DIR . '/function/vaildata.php');

//错误处理和记录
register_shutdown_function('___Error');

//自动加载处理
\fcphp\core\LoaderClass::register();

//定义一个常量,表示总工作目录(根目录)
GetRootDir(PATH, 'HOME_PATH');

//定义一个常量,表示工作目录(根目录)
GetRootDir(SPATH, 'APP_PATH');

//定义一个常量,表示框架目录(根目录)
GetRootDir(FC_DIR, 'FC_PATH');

//获取当前地址，兼容方案
define('NOW_URL', RequestUri());

//定义基于当前目录的完整域名
define('HOME_URL', HTTP_SCHEMA . HTTP_HOST . APP_PATH);

//判断url模式
UrlMode();

//判断设置配置文件夹名
defined('CONFIG_DIRNAME') || define('CONFIG_DIRNAME', 'config');

//判断定义日志目录
defined('LOG_DIR') || define('LOG_DIR', PATH . '/log');

//判断定义扩展目录
defined('EXTEND_DIR') || define("EXTEND_DIR", PATH . '/extend');

//定义子目录的名称
define('APP_NAME', str_replace(HOME_PATH, '', APP_PATH));

//公共配置目录
define("CONFIG_COMMON", PATH . '/' . CONFIG_DIRNAME . '/common');

//主配置目录
define("CONFIG_DIR", PATH . '/' . CONFIG_DIRNAME);

//子目录配置目录
define("CONFIG_APP_DIR", SPATH . '/' . CONFIG_DIRNAME);

//加载初始化类
$load = GetObj('loadStart', 'start');

//第三方扩展设置
$load->ExtendConfig(EXTEND_DIR . '/config.php');

//自定义初始化类，用来进行一些初始化工作
try {
    $init = GetObj('initStart', 'start');
    $init->run();
} catch (\Exception $e) {
    \ErrorShow($e->getMessage()); //打印错误
}
