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
DROP TABLE IF EXISTS `cmf_freight`;
CREATE TABLE `cmf_freight` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(40) NOT NULL COMMENT '模板名称',
  `create_time` char(11) NOT NULL COMMENT '添加时间',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '计费规则(1.包邮 2.按件数)',
  `default_num` int(11) DEFAULT '1' NOT NULL COMMENT '默认首件',
  `default_price` decimal(11,2) DEFAULT '0.00' NOT NULL COMMENT '默认首费',
  `continue_num` int(11) DEFAULT '1' NOT NULL COMMENT '续件',
  `continue_price` decimal(11,2) DEFAULT '0.00' NOT NULL COMMENT '续费',
  `free_shipping` char(11) DEFAULT '0' NOT NULL COMMENT '满足几件包邮(包邮的状态下)',
  `shopid` int(11) DEFAULT '1' COMMENT '店铺id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='默认运费表';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
