/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-11-18 09:50:09
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_ali_pay_refund
-- ----------------------------
DROP TABLE IF EXISTS `cmf_ali_pay_refund`;
CREATE TABLE `cmf_ali_pay_refund` (
  `out_trade_no` varchar(255) NOT NULL COMMENT '交易单号',
  `transaction_id` varchar(255) NOT NULL COMMENT '商户单号',
  `create_time` char(20) NOT NULL COMMENT '创建时间',
  `total_fee` decimal(8,2) NOT NULL COMMENT '总金额',
  `refund_fee` decimal(8,2) NOT NULL COMMENT '退款金额',
  `yrefund_fee` decimal(8,2) NOT NULL COMMENT '已退款金额',
  `out_refund_no` varchar(255) NOT NULL COMMENT '退款单号',
  `refund_id` varchar(255) NOT NULL COMMENT '退款交易单号',
  `buyer_logon_id` char(40) DEFAULT NULL COMMENT '退回到支付宝用户的手机号码',
  `buyer_user_id` char(100) DEFAULT NULL COMMENT '退回到支付宝用户的id',
  `fund_change` char(20) DEFAULT NULL COMMENT '退回币种',
  `refund_reason` varchar(255) DEFAULT NULL COMMENT '退款描述'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付宝支付_退款表';
-- ----------------------------
-- Records of cmf_ali_pay_refund
-- ----------------------------
