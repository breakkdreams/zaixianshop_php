/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-11-18 09:50:09
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_ali_pay_transfer
-- ----------------------------
DROP TABLE IF EXISTS `cmf_ali_pay_transfer`;
CREATE TABLE `cmf_ali_pay_transfer` (
  `out_trade_no` varchar(255) DEFAULT NULL COMMENT '商户单号',
  `order_id` varchar(255) DEFAULT NULL COMMENT '支付宝转账单据号',
  `create_time` char(80) DEFAULT NULL COMMENT '支付时间',
  `account` varchar(80) DEFAULT NULL COMMENT '支付宝账号',
  `name` char(40) DEFAULT NULL COMMENT '姓名',
  `money` decimal(8,2) DEFAULT NULL COMMENT '金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付宝转账表';
-- ----------------------------
-- Records of cmf_ali_pay_transfer
-- ----------------------------
