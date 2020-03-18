-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-18 06:50:51
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
-- 表的结构 `cmf_notice_verify`
--

CREATE TABLE IF NOT EXISTS `cmf_notice_verify` (
  `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(40) DEFAULT NULL COMMENT '短信模板编号',
  `mobile` varchar(11) DEFAULT NULL COMMENT '用户手机',
  `content` varchar(255) DEFAULT NULL COMMENT '发送内容',
  `id_code` varchar(10) DEFAULT NULL COMMENT '验证密码',
  `key` varchar(255) DEFAULT NULL COMMENT 'AccessKeyId',
  `key_secret` varbinary(120) DEFAULT NULL COMMENT 'AccessKeySecret',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态 0默认未使用 1 使用过',
  `sign` varchar(255) DEFAULT NULL COMMENT '短信签名',
  `sendname` varchar(255) DEFAULT '系统消息' COMMENT '发件人',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `ip` varchar(255) DEFAULT NULL COMMENT 'IP地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息记录' AUTO_INCREMENT=122 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
