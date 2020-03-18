-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-18 06:49:45
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
-- 表的结构 `cmf_notice`
--

CREATE TABLE IF NOT EXISTS `cmf_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `release_name` varchar(30) NOT NULL COMMENT '发布人',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `release_time` int(11) NOT NULL COMMENT '发布时间',
  `status` int(11) NOT NULL COMMENT '1.待发布 2.审核中 3.通过 4.拒绝',
  `is_jinyong` int(11) NOT NULL COMMENT '是否被禁',
  `refuse_reason` varchar(255) NOT NULL COMMENT '拒绝原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
