<?php
return array(

    'default' => array(

        'tplbegin' => '{(',

        //左分割符

        //右分割符
        'tplend' => ')}',

        //模版后缀
        'suffix' => 'html',

        //模版文件路径
        //'dirs' => PATH.'/app/view',

        //'tempdirs' => PATH .'/app/runtime',

        //模板文件错误，要跳转的地址，可以是绝对地址,默认为文字提示
        'errorurl' => Url('404.html',false,true,3),

        //强制编译
        'tempopen' => true,

        //引用编译
        'includeopen' => true,

    ),

    //自定义模板替换，请注意这里是通用的，所有的配置都可以用哦
    'TPL_REPLACE' => array(

        //文件引用的简写[include(模版名称或者文件路径)]
        '#\[include\((.*)\)\]#isuU' => '{(include file="\\1")}',

        //文件引用的简写{引用：模版名称或者文件路径}
        '#\{引用：(.*)\}#isuU' => '{(include file="\\1")}',

        //定义一个路径
        '#_STATIC_#' => '{(STATIC_DIR)}',

        //引入js的简写
        '#\[js=(.*)\]#' => '<script src="\\1" type="text/javascript"></script>',


        //jquery的简写
        '#\[jquery\]#' => 'http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js',

        //检测并输出变量（传入的变量）[@a]
        '#\[\@([_A-Za-z.0-9]+)\]#isuU' => '{(if isset(@\\1))}{(@\\1)}{(/if)}',

        //检测并输出变量（传入的变量） <<@a>>
        '#\<<\@([_A-Za-z.0-9]+)\>>#isuU' => '{(if isset(@\\1))}{(@\\1)}{(else)}0{(/if)}',

        //检测并输出,前面可以加函数名 [md5 @a]
        '#\[([_A-Za-z.0-9]+)\s+\@([_A-Za-z.0-9]+)\]#isuU' => '{(if isset(@\\2))}{(\\1(@\\2))}{(/if)}',

        //检测并输出,前面可以加函数名  [md5 $a]
        '#\[([_A-Za-z.0-9]+)\s+\$([_A-Za-z.0-9]+)\]#isuU' => '{(if isset($\\2))}{(\\1($\\2))}{(/if)}',

        //检测并输出,前面可以加函数名 [md5 @a]
        '#\[!([_A-Za-z.0-9]+)\s+\@([_A-Za-z.0-9]+)\]#isuU' => '{(if isset(@\\2))}{(!\\1(@\\2))}{(/if)}',

        //检测并输出,前面可以加函数名  [md5 $a]
        '#\\[!([_A-Za-z.0-9]+)\s+\$([_A-Za-z.0-9]+)\]#isuU' => '{(if isset($\\2))}{(!\\1($\\2))}{(/if)}',

        //循环数组加默认 [list default="没有"][$value.get][/list]
        '#\[list="([_A-Za-z-0-9.@$]+)"\s+default="(.*)"\]([\w\W]+?)\[\/list\]#' => '{(if isset(\\1) && is_array(\\1))}{(foreach \\1)}\\3{(/foreach)}{(else)}\\2{(/if)}',

        //循环无默认 [list][$value.get][/list]
        '#\[list="([_A-Za-z0-9.@$]+)"\]([\w\W]+?)\[\/list\]#' => '{(if isset(\\1) && is_array(\\1))}{(foreach \\1)}\\2{(/foreach)}{(/if)}',

        //如果不是pjax
        '#\<!--pjaxhide-->([\w\W]+?)\<!--\/pjaxhide-->#' => '{(if isPjax()===false)}\\1{(/if)}',

        //输出时间
        '#\[time="(.*)"\]#' => '{(date("Y-m-d H:i",\\1))}',

        //静态文件地址
        '#_HTTPSTATIC_#' => '{(HTTP_STATIC_DIR)}',

        '#_THEMES_#' => '{(STATIC_DIR)}/themes',

        '#_ADMIN_#' => '{(STATIC_DIR)}/themes/admin',

        '#_STATIC_#' => '{(STATIC_DIR)}',

        '#_HOMEURL_#' => '{(HOME_URL)}',

        //标题
        '#{页面标题}#' => '{(isset(@title)?@title:PAGE_TITLE)}',
        
        //SEO关键词
        '#{页面关键词}#' => '{(isset(@keywords)?@keywords:PAGE_KEYWORDS)}',        
 
        //SEO描述
        '#{页面描述}#' => '{(isset(@description)?@description:PAGE_DESCRIPTION)}', 
        
        //未登录显示
        // '#\<!--nologin-->([\w\W]+?)\<!--\/nologin-->#' => '{(if isUserLogin()===false)}\\1{(/if)}',
        '#\[图片ID\((.*)\)\]#' => '{(getViewPic(\\1))}',
        
        '#{昵称}#' => '{(if $nick=getUserInfo("nick"))}{($nick)}{(/if)}',
        
        '#{昵称}#' => '{(if $nick=getUserInfo("nick"))}{($nick)}{(/if)}',

        '#{邮箱}#' => '{(if $email=getUserInfo("email"))}{($email)}{(/if)}',

        '#{网址}#' => '{(if $homeurl=getUserInfo("homeurl"))}{($homeurl)}{(/if)}',

        '#{头像}#' => '{(if $imgurl=getUserInfo("imgurl"))}{($imgurl)}{(else)}{(STATIC_DIR)}/avatar/default.jpg{(/if)}',
		
		'#{首页链接}#' => '{(Url("index.html",false,true,3))}',
		'#{推荐链接}#' => '{(Url("recommend.html",false,true,3))}',	
		'#{关于链接}#' => '{(Url("about.html",false,true,3))}',		
		'#{友链链接}#' => '{(Url("link.html",false,true,3))}',			
    ),

);
