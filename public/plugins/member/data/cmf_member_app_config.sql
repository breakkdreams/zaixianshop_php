-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019-12-20 14:37:15
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
-- 表的结构 `cmf_member_app_config`
--

CREATE TABLE IF NOT EXISTS `cmf_member_app_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catname` char(50) NOT NULL COMMENT '栏目名称',
  `icon` varchar(255) NOT NULL COMMENT '栏目图标',
  `type` tinyint(1) NOT NULL COMMENT '类型（1.网页端  2APP端）',
  `status` tinyint(1) NOT NULL COMMENT '状态（1显示 0隐藏）',
  `url` varchar(255) NOT NULL COMMENT '栏目链接',
  `sort` mediumint(9) NOT NULL DEFAULT '500' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
