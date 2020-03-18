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
DROP TABLE IF EXISTS `cmf_freight_item`;
CREATE TABLE `cmf_freight_item` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `freight_id` int(11) NOT NULL COMMENT '模板id',
  `create_time` char(11) NOT NULL COMMENT '添加时间',
  `area` text COMMENT '配送地区',
  `first_num` int(11) DEFAULT '1' NOT NULL COMMENT '首件',
  `first_price` decimal(11,2) DEFAULT '0.00' NOT NULL COMMENT '首费',
  `continue_num` int(11) DEFAULT '1' NOT NULL COMMENT '续件',
  `continue_price` decimal(11,2) DEFAULT '0.00' NOT NULL COMMENT '续费',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='地区运费表';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
