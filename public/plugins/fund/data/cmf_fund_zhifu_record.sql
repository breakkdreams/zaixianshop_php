-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020-01-15 15:20:24
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
-- 表的结构 `cmf_fund_zhifu_record`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_zhifu_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `trade_sn` varchar(255) NOT NULL COMMENT '订单号',
  `username` varchar(20) DEFAULT NULL COMMENT '用户名',
  `nickname` varchar(19) DEFAULT NULL COMMENT '昵称',
  `phone` int(19) DEFAULT NULL COMMENT '用户手机',
  `type` int(1) NOT NULL COMMENT '用户类型 1为支付宝   2为微信  3为银行',
  `amount` decimal(8,2) NOT NULL COMMENT '交易金额',
  `addtime` int(18) NOT NULL COMMENT '添加时间',
  `uptime` int(18) DEFAULT NULL COMMENT '修改时间',
  `bid` int(11) DEFAULT NULL COMMENT '账户id',
  `status` int(1) NOT NULL COMMENT '1为生成订单  2为成功  3为失败   4为退款',
  `transaction_id` varchar(33) DEFAULT NULL COMMENT '交易商户单号',
  `sid` text NOT NULL COMMENT '订单id（数组）',
  `title` varchar(220) NOT NULL COMMENT '标题',
  `del` int(1) NOT NULL DEFAULT '1' COMMENT '1为未删   2为删除 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cmf_fund_zhifu_record`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
