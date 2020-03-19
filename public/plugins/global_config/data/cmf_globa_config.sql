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
DROP TABLE IF EXISTS `cmf_globa_config`;
CREATE TABLE `cmf_global_config` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(40) COMMENT 'key值',
  `content` char(40) COMMENT 'key对应的值',
  `describe` char(100) COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='配置表';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
