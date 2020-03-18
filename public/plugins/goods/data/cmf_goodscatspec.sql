-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-11 02:26:48
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
-- 表的结构 `cmf_goodscatspec`
--

CREATE TABLE IF NOT EXISTS `cmf_goodscatspec` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gcatid` int(11) NOT NULL DEFAULT '0',
  `gcatpath` varchar(100) NOT NULL,
  `specname` varchar(255) NOT NULL,
  `isallowimg` tinyint(1) NOT NULL DEFAULT '0',
  `isshow` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(11) DEFAULT '50',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

