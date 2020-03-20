/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-10-25 16:17:33
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_demo
-- ----------------------------
DROP TABLE IF EXISTS `cmf_crowd_funding`;
CREATE TABLE `cmf_crowd_funding` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COMMENT '商品名称',
  `img_path` varchar(255) COMMENT '商品缩略图',
  `img_path_list` varchar(255) COMMENT '商品图片',
  `describe` varchar(255) COMMENT '商品描述',
  `price` varchar(100) COMMENT '产品价格',
  `num` varchar(100) COMMENT '众筹份数',
  `start_time` varchar(255) COMMENT '开始时间',
  `end_time` varchar(255) COMMENT '结束时间',
  `info` varchar(100) COMMENT '详情',
  `show_index` int(11) default 0 COMMENT '是否首页显示 1.显示',
  `create_time` int(20) default 0 COMMENT '添加时间',
  `sale` int(20) default 0 COMMENT '上下架状态 1.上架',
  `state` int(20) default 0 COMMENT '当前状态 0.未开始 1.进行中 2.已结束',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='众筹商品';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
