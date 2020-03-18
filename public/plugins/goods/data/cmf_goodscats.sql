-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-11 02:25:57
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
-- 表的结构 `cmf_goodscats`
--

CREATE TABLE IF NOT EXISTS `cmf_goodscats` (
  `catid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `catname` varchar(20) NOT NULL,
  `isshow` tinyint(4) NOT NULL DEFAULT '1',
  `isfloor` tinyint(4) NOT NULL DEFAULT '1',
  `ischild` int(11) NOT NULL DEFAULT '0' COMMENT '是否有下级（1有  0无）',
  `catsort` int(11) NOT NULL DEFAULT '50',
  `catimg` varchar(255) DEFAULT '',
  PRIMARY KEY (`catid`),
  KEY `pid` (`pid`,`isshow`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

