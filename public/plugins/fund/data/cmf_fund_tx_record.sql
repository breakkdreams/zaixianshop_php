-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020-01-15 15:20:34
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
-- 表的结构 `cmf_fund_tx_record`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_tx_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trade_sn` varchar(100) NOT NULL COMMENT '提现订单号',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `username` varchar(100) NOT NULL COMMENT '用户名',
  `nickname` varchar(100) NOT NULL COMMENT '用户昵称',
  `phone` varchar(100) NOT NULL COMMENT '手机号码',
  `type` tinyint(4) NOT NULL COMMENT '提现类型',
  `account` varchar(255) NOT NULL COMMENT '提现账号',
  `accountname` varchar(255) NOT NULL COMMENT '提现账号名称',
  `amount` decimal(8,2) NOT NULL COMMENT '提现金额',
  `reason` varchar(255) DEFAULT NULL COMMENT '退回原因',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '提现状态  1提交申请 2通过  3 不通过',
  `addtime` int(11) NOT NULL COMMENT '提现时间',
  `uptime` int(18) DEFAULT NULL COMMENT '修改时间',
  `make` varchar(255) DEFAULT NULL COMMENT '备注',
  `del` int(1) NOT NULL DEFAULT '1' COMMENT '1为未删   2为删除 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cmf_fund_tx_record`
--



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
