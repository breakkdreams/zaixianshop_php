-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020-01-15 15:21:08
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
-- 表的结构 `cmf_fund_pay_record`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_pay_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trade_sn` varchar(255) NOT NULL COMMENT '交易订单号',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `username` varchar(100) NOT NULL COMMENT '用户名',
  `nickname` varchar(100) NOT NULL COMMENT '用户昵称',
  `phone` varchar(100) NOT NULL COMMENT '手机',
  `type` tinyint(4) NOT NULL COMMENT '交易类型',
  `amount` decimal(8,2) NOT NULL COMMENT '交易金额',
  `addtime` int(11) NOT NULL COMMENT '交易时间',
  `uptime` int(18) DEFAULT NULL COMMENT '修改时间',
  `bid` int(11) DEFAULT NULL COMMENT '账号id',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT '交易单号',
  `title` varchar(225) NOT NULL COMMENT '标题',
  `del` int(1) NOT NULL DEFAULT '1' COMMENT '1为未删   2为删除 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cmf_fund_pay_record`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
