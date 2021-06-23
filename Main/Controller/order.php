<?php

namespace Main\Controller;

use FC\Controller\BaseController;

/*
 * 订单高并发测试案例
 * 需要导入Sql/order.sql,需要redis，经过测试，协议类亦可用
 * @Author: lovefc 
 * @Date: 2019-10-16 16:08:04 
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 11:08:37
 */

class order extends BaseController
{
	
	// 实验结果
	public function index(){
		echo "测试命令: ab -n 1000 -c 1000 ".HOST_DOMAIN."/order/new".FC_EOL;
		$num = $this->MYSQL->table('order')->number();
        echo '订单数据: '.$num.FC_EOL;	
		$num2 = $this->MYSQL->table('log')->number();
        echo '日志数据: '.$num2.FC_EOL;
        $num3 = $this->MYSQL->table('store')->where(['id'=>1])->value('number');	
		echo '商品剩余数量: '.$num3.FC_EOL;
		$sql = "select order_sn,count(*) as count from `order` group by order_sn having count>1";
		$re = $this->MYSQL->query($sql)->fetch();
		if($num != 0){
	        echo '是否出现重复订单: ';
		    if(!$re){
			    echo '未出现'.FC_EOL;
		    }else{
			    echo '已出现'.FC_EOL;
		    }
		    echo '<a href="'.HOST_DOMAIN.'/order/clean">点此初始化所有数据</a>';
		}
	}
	
	
    // 模拟下单 并发测试 ab -c 1000 -n 1000 http://xxx/Main/order/new
    public function new()
    {
        $key = 'sku_id';
        $sku_id = 11;
        // 使用redis队列，因为pop操作是原子的
        // 获取字段长度
        $count = $this->REDIS->lpop($key);
        if (!$count) {
            $this->log('库存为0');
            die();
        }
        // 插入订单
        $order_sn = $this->build_order_no();
        $data = [
           'order_sn' => $order_sn,
           'user_id' => 1,
           'goods_id' => 1,
           'sku_id' => 11,
           'price' => 10
        ];
        //库存减少
		if($this->check_order_sn($order_sn)){
			$this->log('订单号已存在');
			die();
		}
        $sql2 = "update store set number=number-1 where sku_id='{$sku_id}'";
        if ($this->MYSQL->table('order')->add($data) && $this->MYSQL->query($sql2)) {
            $this->log('下单成功');
        } else {
            $this->log('下单失败');
        }
    }

    // 设置redis
    private function setkey($key, $count)
    {
        $redis = $this->REDIS;
        for ($i = 0; $i < $count; $i++) {
            $redis->lpush($key, 1);
        }
    }

    // 生成订单号
    private function build_order_no()
    {
        return date('ymd') . (strtotime(date('YmdHis', time()))) . substr(microtime(), 2, 6) . sprintf('%03d', rand(0, 999));
    }
	
    // 检查订单是否存在
    private function check_order_sn($order_sn)
    {
        $where['order_sn'] = $order_sn;
		return $this->MYSQL->table('order')->where($where)->has();
    }
	
    //记录日志
    private function log($event, $type = 0)
    {
        $sql = "insert into log(event,type)values('{$event}','{$type}')";
        $this->MYSQL->query($sql);
        echo $event;
    }
    
    // 初始化所有变量
    public function clean(){
        // 清空商品数据表
        $this->MYSQL->cleanTable('order');
        // 清空日志数据表
        $this->MYSQL->cleanTable('log');        
        // 重新设置库存
        $this->MYSQL->table('store')->where(['sku_id'=>11])->upd(['number'=>100]);
        // 删除所有的key
        $this->REDIS->flushall();
        // 在redis中设置一下sku的数量。
        $this->setkey('sku_id', 100); 
		header('location:'.getenv("HTTP_REFERER"));
    }
}
