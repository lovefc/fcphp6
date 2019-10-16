--
-- 表的结构 `goods`
--
 
CREATE TABLE IF NOT EXISTS `goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  PRIMARY KEY (`goods_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='商品表' AUTO_INCREMENT=2;
 
 
--
-- 转存表中的数据 `goods`
--
 
 
INSERT INTO `goods` (`goods_id`, `cat_id`, `goods_name`) VALUES
(1, 0, '小米手机');
 
-- --------------------------------------------------------
 
--
-- 表的结构 `log`
--
 
CREATE TABLE IF NOT EXISTS `log` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `event` varchar(255) NOT NULL,
 `type` tinyint(4) NOT NULL DEFAULT '0',
 `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='日志表' AUTO_INCREMENT=1;
 
 
-- --------------------------------------------------------

--
-- 表的结构 `order`
--
 
CREATE TABLE IF NOT EXISTS `order` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `order_sn` varchar(32) NOT NULL,
 `user_id` int(11) NOT NULL,
 `goods_id` int(11) NOT NULL DEFAULT '0',
 `sku_id` int(11) NOT NULL DEFAULT '0',
 `price` float NOT NULL,
 `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表' AUTO_INCREMENT=1;
 
-- --------------------------------------------------------
 
--
-- 表的结构 `store`
--
 
CREATE TABLE IF NOT EXISTS `store` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `goods_id` int(11) NOT NULL,
 `sku_id` int(10) unsigned NOT NULL DEFAULT '0',
 `number` int(10) NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='库存表' AUTO_INCREMENT=2;
 
--
-- 转存表中的数据 `store`
--
INSERT INTO `store` (`id`, `goods_id`, `sku_id`, `number`) VALUES
(1, 1, 11, 500);