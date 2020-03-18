-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-11 02:30:55
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
-- 表的结构 `cmf_goods_specs`
--

CREATE TABLE IF NOT EXISTS `cmf_goods_specs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shopid` int(11) NOT NULL DEFAULT '0',
  `goodsid` int(11) NOT NULL DEFAULT '0',
  `specids` varchar(255) NOT NULL,
  `marketprice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `specprice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `specstock` int(11) NOT NULL DEFAULT '0',
  `salenum` int(11) NOT NULL DEFAULT '0',
  `isdefault` tinyint(4) DEFAULT '0',
  `isok` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效值',
  PRIMARY KEY (`id`),
  KEY `shopid` (`goodsid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


