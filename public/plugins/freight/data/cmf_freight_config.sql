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
DROP TABLE IF EXISTS `cmf_freight_config`;
CREATE TABLE `cmf_freight_config` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `symbol` char(40) NOT NULL COMMENT '模板名称',
  `methods` char(40) NOT NULL COMMENT '模板名称',
  `param` char(40) NOT NULL COMMENT '模板名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='运费配置表';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
