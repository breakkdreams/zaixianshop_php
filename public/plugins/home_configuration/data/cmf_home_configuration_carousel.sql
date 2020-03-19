-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-12 05:21:44
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zysubsystem20190906`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_home_configuration_carousel`
--

CREATE TABLE IF NOT EXISTS `cmf_home_configuration_carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `carousel_name` varchar(200) DEFAULT NULL COMMENT '轮播图名称',
  `img_url` varchar(200) DEFAULT NULL COMMENT '图片路径url',
  `jump_url` varchar(200) DEFAULT NULL COMMENT '跳转链接',
  `type` int(11) DEFAULT NULL COMMENT 'type 1.APP端 2. WEB端',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '2' COMMENT '状态 1.使用中 2.备用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页配置-轮播图表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
