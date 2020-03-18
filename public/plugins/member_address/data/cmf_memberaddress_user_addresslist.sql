-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-02 06:43:35
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
-- 表的结构 `cmf_memberaddress_user_addresslist`
--

CREATE TABLE IF NOT EXISTS `cmf_memberaddress_user_addresslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `receive_name` varchar(200) NOT NULL COMMENT '收件人名称',
  `receive_phone` bigint(20) NOT NULL COMMENT '收件人电话',
  `cri_code` varchar(40) NOT NULL COMMENT '地区代码',
  `cri_name` varchar(200) NOT NULL COMMENT '地区名称',
  `address` varchar(255) NOT NULL COMMENT '详细地址',
  `show_address` varchar(255) DEFAULT NULL COMMENT '地址展示',
  `postal_code` varchar(11) DEFAULT NULL COMMENT '邮政编码',
  `is_default` int(11) NOT NULL DEFAULT '2' COMMENT '是否是默认地址 1是 2 不是',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户地址列表' AUTO_INCREMENT=22 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
