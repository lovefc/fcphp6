<?php

/**
 * 定义一些类所要使用的配置扩展
 * 这里要注意的问题有
 * 如果你直接指定了一个文件的路径，那么将会使用其中的配置
 * 如果你使用了一个文件名，例如db.php ,其实就是访问配置目录下的db.php 子目录的该配置目录下的db.php会继承并且覆盖父级目录的配置
 * 如果你使用了 public.php::IMAGE 那么会读取public.php中的IMAGE的键值
 * 嫌烦？那就直接使用数组吧
 * 
 * @Author: lovefc 
 * @Date: 2019-09-16 15:49:57 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-20 13:42:09
 */


return [
   'FC\Glue\Routes'  => 'route.php',
   'FC\Glue\Load'    => 'loader.php',
];
