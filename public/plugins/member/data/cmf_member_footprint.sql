-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019-12-20 14:37:28
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
-- 表的结构 `cmf_member_footprint`
--

CREATE TABLE IF NOT EXISTS `cmf_member_footprint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` smallint(8) DEFAULT NULL COMMENT '用户id',
  `goods_id` smallint(8) DEFAULT NULL COMMENT '商品id',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `amount` decimal(8,2) DEFAULT NULL COMMENT '价格',
  `create_time` char(11) DEFAULT NULL COMMENT '添加时间',
  `footprint_time` char(11) DEFAULT NULL COMMENT '日期(2018-02-24 01:00:00)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=446 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
