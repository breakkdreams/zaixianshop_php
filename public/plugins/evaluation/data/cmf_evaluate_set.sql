/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-11-18 09:50:09
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_evaluate_set
-- ----------------------------
DROP TABLE IF EXISTS `cmf_evaluate_set`;
CREATE TABLE `cmf_evaluate_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `activate` int(11) NOT NULL COMMENT '1：已激活 0：未激活',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
-- ----------------------------
-- Records of cmf_evaluate_set
-- ----------------------------
