-- mysql导入此文件进行测试 --
DROP TABLE IF EXISTS ceshi;
CREATE TABLE `ceshi` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(10) NOT NULL COMMENT '姓名',
  `age` int(3) NOT NULL COMMENT '年纪',
  PRIMARY KEY(id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `ceshi` (id, name, age) VALUES (1, '封尘', 26);
