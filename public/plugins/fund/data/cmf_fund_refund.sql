-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020-01-15 15:21:01
-- 服务器版本: 5.6.14
-- PHP 版本: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `shop`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_fund_refund`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `trade_sn` varchar(122) NOT NULL COMMENT '退款单号',
  `refund_uid` int(11) NOT NULL COMMENT '退款用户id',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `status` int(1) DEFAULT '1' COMMENT '1为退款到余额',
  `refund_money` decimal(8,2) NOT NULL COMMENT '退款金额',
  `type` int(1) NOT NULL COMMENT '1为充值退款  2为支付退款',
  `stat` int(1) NOT NULL COMMENT '1为退款成功  2为失败',
  `refund_time` varchar(19) NOT NULL COMMENT 't退款时间',
  `del` int(1) NOT NULL DEFAULT '1' COMMENT '1为未删   2为删除 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cmf_fund_refund`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
