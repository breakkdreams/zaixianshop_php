-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-12 05:21:38
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
-- 表的结构 `cmf_home_configuration_skin`
--

CREATE TABLE IF NOT EXISTS `cmf_home_configuration_skin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增主键ID',
  `skin_name` varchar(200) DEFAULT NULL COMMENT '皮肤名称',
  `img_url` varchar(200) DEFAULT NULL COMMENT '皮肤展示图片url',
  `type` int(11) DEFAULT NULL COMMENT '分类1,.APP  2.WEB',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '2' COMMENT '状态：1为当前设置皮肤 2.备选皮肤',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='首页设置皮肤表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
