-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-23 07:10:41
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zysubsystem20190906`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_after_sales`
--

CREATE TABLE IF NOT EXISTS `cmf_after_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `reason` varchar(255) NOT NULL COMMENT '退款理由',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `remark` text NOT NULL COMMENT '退款说明',
  `proof` text NOT NULL COMMENT '凭证',
  `time` int(11) NOT NULL COMMENT '申请时间',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `status` int(11) NOT NULL COMMENT '1.审核中 2.待退货 3.进行中 4.已完成',
  `refund_ordersn` varchar(20) NOT NULL COMMENT '退款订单编号',
  `goods_id` varchar(11) NOT NULL COMMENT '商品id',
  `lx_name` varchar(60) NOT NULL COMMENT '联系人',
  `lx_mobile` varchar(60) NOT NULL COMMENT '联系电话',
  `shipper_name` varchar(255) NOT NULL,
  `shipper_code` varchar(255) NOT NULL,
  `logistics_order` varchar(255) NOT NULL,
  `fhtime` int(11) NOT NULL COMMENT '发货时间',
  `tksucc_time` int(11) NOT NULL COMMENT '退款成功时间',
  `transaction_id` varchar(255) NOT NULL COMMENT '交易单号',
  `pay_type` int(11) NOT NULL COMMENT '支付方式 1:支付宝 2:微信 3:银行卡 4.余额',
  `audit_status` int(11) NOT NULL COMMENT '审核状态 1.通过 2.拒绝',
  `refuse_cause` varchar(255) NOT NULL COMMENT '拒绝原因',
  `dianpu_name` varchar(255) NOT NULL COMMENT '店铺名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='售后订单' AUTO_INCREMENT=32 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
