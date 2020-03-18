-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-18 06:50:10
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shop`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_notice_email`
--

CREATE TABLE IF NOT EXISTS `cmf_notice_email` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) NOT NULL COMMENT '邮箱',
  `posttime` int(11) NOT NULL COMMENT '提交时间',
  `id_code` varchar(10) NOT NULL COMMENT '验证密码',
  `subject` varchar(200) DEFAULT '' COMMENT '邮件标题',
  `msg` varchar(90) DEFAULT NULL COMMENT '邮件内容',
  `send_userid` mediumint(8) NOT NULL COMMENT '发件人',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '默认0  1使用过',
  `return_id` varchar(30) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=17 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
