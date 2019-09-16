<?php
namespace fcphp\start;
use fcphp\extend\Image;

class imgStart extends Image
{
    use \fcphp\traits\Parents;//继承
    
    //设置配置名称
    //必须是静态方法
    //返回的值可以是一个文件路径,会读取里面的配置
    //也可以是一个字符串，会作为键名在默认配置中查找
    //还可以是一个文件名,例如public.php，
    //这样的会在框架配置目录和工作目录里的配置目录里查找这个文件并读取，并且会合并这两个配置文件，相同的配置，工作目录里的会覆盖
    //后面可以跟上键值,例如public.php::VERIFY,就会读取public.php里的配置，并返回键名为VERIFY的值
    //如果有多维数组，可以在后面在加上键名，例如public.php::VERIFY::CONFIG
    /*
     * $yzm session的键名
     */
    public function verify($yzm='verify')
    {
        $this->Imageout($yzm);
    }
    
    
    /*
     * 缩放图片
     * $url 图片地址
     * $w 图片的新宽度
     * $h 图片的新高度
     * 返回值，返回缩放后的路径
     */
    public function cresimg($url, $w = 50, $h = 50)
    {
        if (empty($url)) {
            if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
                return false;
            }
        }
        $width  = is_numeric($w) ? $w : false;
        
        $height = is_numeric($h) ? $h : false;
        
        $size = $w . 'x' . $h; // 图片大小名
        
        $img  = $simg = null;
        
        $url = urldecode($url); // url解码
        
        $md5 = md5($url);
        
        // 如果地址是一个url，那么就拉取它
        if (IsUrl($url)) {
            $imgurl = $this->urlpath . '/' . $url . basename($url);
            UpUrl($url, $imgurl);
        } else {
            $imgurl = $url;
        }
        
        $simg  = $this->simgpath . '/' . $size .$md5. basename($url);//读取本地缓存
        
        if (is_file($simg)) {
            return $simg;
        }
        
        if ($width) {
            
            $image = $this->setimg($imgurl);
            
            // 获取图片信息
            $info  = $image->getImageInfo($imgurl);
            
            // 获取图片原来的大小
            if (!empty($info[0])) {
                $old_w = $info[0];
                $old_h = $info[1];
            }
            if(!$h){
                $h = $old_h;   
            }
            // 如果图片的高度大于图片的宽度
            if ($old_h > $old_w) {
                // 这里就允许裁剪了
                $image->thumb($simg, $w, $h, 1);
            } else {
                $image->thumb($simg, $w, $h);
            }
        }
        $simg != null ? $simg : $img;
        if (is_file($simg)) {
            return $simg;
        }
        return false;
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}
