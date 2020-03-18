-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019-12-20 14:37:12
-- 服务器版本: 5.6.14
-- PHP 版本: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `zyanswer`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_member`
--

CREATE TABLE IF NOT EXISTS `cmf_member` (
  `uid` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL COMMENT '用户账号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `islock` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '锁定：2是，1否',
  `vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'vip：0否，1是',
  `overduedate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'vip过期时间',
  `nickname` varchar(100) NOT NULL COMMENT '昵称',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `create_time` int(11) unsigned NOT NULL COMMENT '注册时间',
  `last_login_time` int(11) unsigned DEFAULT NULL COMMENT '最后登录时间',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `mobile` char(20) DEFAULT NULL COMMENT '手机号',
  `last_login_ip` varchar(15) NOT NULL COMMENT '最后登录ip',
  `create_ip` varchar(15) NOT NULL COMMENT '注册ip',
  `groupid` tinyint(2) unsigned NOT NULL COMMENT '会员组',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员主表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cmf_member`
--

INSERT INTO `cmf_member` (`uid`, `username`, `avatar`, `islock`, `vip`, `overduedate`, `nickname`, `password`, `create_time`, `last_login_time`, `email`, `mobile`, `last_login_ip`, `create_ip`, `groupid`) VALUES 
(1, 'zc54W1rc', '', 1, 1, 2020, '管理员', '###8a5745e3d312f97f678853c603af8e90', 1577838780, 0, '300c@300c.cn', '15000000000', '', '192.168.1.4', 1);
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
