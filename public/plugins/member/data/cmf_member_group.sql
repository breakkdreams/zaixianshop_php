-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019-12-20 14:37:32
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
-- 表的结构 `cmf_member_group`
--

CREATE TABLE IF NOT EXISTS `cmf_member_group` (
  `groupid` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(15) NOT NULL COMMENT '用户组名',
  `description` char(100) NOT NULL COMMENT '简洁描述',
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员组表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cmf_member_group`
--

INSERT INTO `cmf_member_group` (`groupid`, `name`, `description`) VALUES
(1, '普通会员', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
