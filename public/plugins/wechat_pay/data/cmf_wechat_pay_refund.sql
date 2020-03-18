/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-10-25 16:17:36
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_wechat_pay_refund
-- ----------------------------
DROP TABLE IF EXISTS `cmf_wechat_pay_refund`;
CREATE TABLE IF NOT EXISTS `cmf_wechat_pay_refund` (
  `out_trade_no` varchar(255) NOT NULL COMMENT '商户单号',
  `transaction_id` varchar(255) NOT NULL COMMENT '交易单号',
  `create_time` char(20) NOT NULL COMMENT '创建时间',
  `total_fee` decimal(8,2) NOT NULL COMMENT '总金额',
  `refund_fee` decimal(8,2) NOT NULL COMMENT '退款金额',
  `out_refund_no` varchar(255) NOT NULL COMMENT '退款单号',
  `refund_id` varchar(255) NOT NULL COMMENT '退款交易单号'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信支付_退款表';
-- ----------------------------
-- Records of cmf_wechat_pay_refund
-- ----------------------------
