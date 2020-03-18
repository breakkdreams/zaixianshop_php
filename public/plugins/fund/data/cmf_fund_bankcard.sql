-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020-01-15 15:21:14
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
-- 表的结构 `cmf_fund_bankcard`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_bankcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` smallint(2) NOT NULL COMMENT '用户ID',
  `username` varchar(100) NOT NULL COMMENT '用户名',
  `nickname` varchar(100) NOT NULL COMMENT '用户昵称',
  `phone` varchar(100) NOT NULL COMMENT '手机号',
  `tid` smallint(2) NOT NULL COMMENT '账号类型:支付宝微信银行卡',
  `account` varchar(255) NOT NULL COMMENT '支付账号',
  `bank_bin` int(18) DEFAULT NULL COMMENT '银行bin',
  `tphone` int(18) DEFAULT NULL COMMENT '验证银行绑定身份证',
  `accountname` varchar(255) NOT NULL COMMENT '支付账号名称',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `is_first` tinyint(2) NOT NULL COMMENT '是否默认账户 1为是  2为否',
  `del` int(1) NOT NULL DEFAULT '1' COMMENT '1为未删   2为删除 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `cmf_fund_bankcard`
--



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
