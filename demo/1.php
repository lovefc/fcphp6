<?php
// 定义编码
header("Content-type:text/html; charset=utf-8");
header("Server: custom-server", true);
header("Fc: 6.0");
header("X-Powered-By: FC/6.0");
header("Server: node/8.8.8");


$json = '[{"id":125,"companyId":9999,"deptName":"开发中心","parentDeptNo":0,"sort":0,"children":[{"id":148,"companyId":9999,"deptName":"开发1号","parentDeptNo":125,"sort":1},{"id":147,"companyId":9999,"deptName":"开发2号","parentDeptNo":125,"sort":0},{"id":146,"companyId":9999,"deptName":"开发3号","parentDeptNo":125,"sort":2}]},{"id":82,"companyId":9999,"deptName":"产品中心","parentDeptNo":0,"sort":4,"children":[{"id":83,"companyId":9999,"deptName":"产品线","parentDeptNo":82,"sort":0,"children":[{"id":84,"companyId":9999,"deptName":"企业内训1","parentDeptNo":83,"sort":4},{"id":80,"companyId":9999,"deptName":"企业内训2","parentDeptNo":83,"sort":2}]}]}]';


$arr = json_decode($json,true);



function xh($arr){
    foreach($arr as $k=>$v){
        if(is_array($v['children']) && count($v['children'])> 0){
            $arr[$k]['children'] = array_multisort($v['children']);
        }        
    }
    return $arr;    
}

array_multisort($arr);


foreach($arr as $k=>$v){
    if(is_array($v['children'])&& count($v['children'])> 0){
        $arr[$k]['children'] = xh($v['children']);
    }        
}
print_r($arr);




/*

function myfunction($value,$key)
{
echo "The key $key <br>";
}

array_walk_recursive($arr,"myfunction");
*/
/*
class cs{
  // 测试
  public static function hello(){
    $lasterror = error_get_last();
    ob_clean();
    print_r($lasterror);
    $file = fopen(__DIR__.'/1.txt','a+b');
    $msg = '22222233333'.PHP_EOL;
    fwrite($file, $msg, 4096);
    fclose($file);
  }
}

$cs = new cs;

define('ERROR',[
   'cs',
   'hello'
]);

register_shutdown_function(ERROR); 

echo new a;
*/