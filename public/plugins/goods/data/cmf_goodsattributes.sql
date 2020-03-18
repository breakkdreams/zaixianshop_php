-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-11 02:25:00
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
-- 表的结构 `cmf_goodsattributes`
--

CREATE TABLE IF NOT EXISTS `cmf_goodsattributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gcatid` int(11) NOT NULL DEFAULT '0',
  `gcatpath` varchar(100) NOT NULL,
  `attrname` varchar(100) NOT NULL,
  `attrtype` tinyint(1) NOT NULL DEFAULT '1',
  `attrval` text,
  `attrsort` int(11) NOT NULL DEFAULT '50',
  `isshow` tinyint(4) DEFAULT '1',
  `isok` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


