/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-10-25 16:17:36
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_wechat_pay
-- ----------------------------
DROP TABLE IF EXISTS `cmf_wechat_pay`;
CREATE TABLE `cmf_wechat_pay` (
  `out_trade_no` varchar(255) NOT NULL COMMENT '交易单号',
  `transaction_id` varchar(255) NOT NULL COMMENT '商户单号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未支付成功，1支付成功',
  `module` char(80) NOT NULL COMMENT '模块标识',
  `create_time` char(20) NOT NULL COMMENT '创建时间',
  `update_time` char(20) NOT NULL COMMENT '更新时间',
  `describe` varchar(255) NOT NULL COMMENT '描述',
  `type` char(10) NOT NULL COMMENT '标识'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信支付表';
-- ----------------------------
-- Records of cmf_wechat_pay
-- ----------------------------
