<?php

namespace Main\Test;

/*
 * 订单高并发测试案例
 * 需要导入Sql/order.sql,需要redis，经过测试，协议类亦可用
 * @Author: lovefc 
 * @Date: 2019-10-16 16:08:04 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-29 08:44:37
 */

class order
{
    use \FC\Traits\Parts;

    // 模拟下单 并发测试 ab -c 6000 -n 5000 http://地址/Main/index.php/order/new
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
        $sql2 = "update store set number=number-1 where sku_id='{$sku_id}'";
        if ($this->DB::table('order')->add($data,false,false) && $this->DB::query($sql2)) {
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
        return date('ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    //记录日志
    private function log($event, $type = 0)
    {
        $sql = "insert into log(event,type)values('{$event}','{$type}')";
        $this->DB::query($sql);
        echo $event;
    }
    
    // 初始化所有变量
    public function clean(){
        // 清空商品数据表
        $this->DB::cleanTable('order');
        // 清空日志数据表
        $this->DB::cleanTable('log');        
        // 重新设置库存
        $this->DB::table('store')->where(['sku_id'=>11])->upd(['number'=>500]);
        // 删除所有的key
        $this->REDIS->flushall();
        // 在redis中设置一下sku的数量。
        $this->setkey('sku_id', 500); 
    }
}
