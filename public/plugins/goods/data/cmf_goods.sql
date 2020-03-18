-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-11-11 02:23:48
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
-- 表的结构 `cmf_goods`
--

CREATE TABLE IF NOT EXISTS `cmf_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodsname` varchar(200) NOT NULL,
  `goodsimg` varchar(150) NOT NULL COMMENT '商品主图',
  `shopid` int(11) NOT NULL,
  `goodstype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品类型（1.实物 2.虚拟）',
  `marketprice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `shopprice` decimal(11,2) NOT NULL DEFAULT '0.00',
  `goodsstock` int(11) NOT NULL DEFAULT '0',
  `issale` tinyint(4) NOT NULL DEFAULT '1' COMMENT '上下架状态（1上架 0下架）',
  `gcatpath` varchar(255) DEFAULT NULL,
  `gcatid` int(11) NOT NULL,
  `brandid` int(11) DEFAULT '0',
  `goodsdesc` text NOT NULL COMMENT '商品描述',
  `goodsinfo` text NOT NULL COMMENT '商品详情图',
  `goodsalbum` text NOT NULL COMMENT '商品相册',
  `goodsstatus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品状态（1正常 2待审核 3审核不通过 4违规）',
  `freight_id` int(11) NOT NULL COMMENT '运费模版id',
  `salenum` int(11) NOT NULL DEFAULT '0',
  `isspec` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否带规格（1是  0否）',
  `illegalremarks` varchar(255) DEFAULT NULL,
  `nopassremarks` varchar(255) NOT NULL COMMENT '审核不通过内容',
  `viewnum` int(11) NOT NULL COMMENT '点击量',
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

