-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-02 06:24:05
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
-- 表的结构 `cmf_order_goods`
--

CREATE TABLE IF NOT EXISTS `cmf_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '关联订单id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_num` int(11) NOT NULL COMMENT '购买数量',
  `goods_img` varchar(255) NOT NULL COMMENT '商品图片',
  `final_price` decimal(10,2) NOT NULL COMMENT '最终价格',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品实际价格',
  `specid` varchar(255) DEFAULT NULL COMMENT '规格参数',
  `specid_name` varchar(255) DEFAULT NULL COMMENT '规格对应中文',
  `is_comment` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否评价（1已评价  0未评价）',
  `dianpu_name` varchar(255) NOT NULL COMMENT '店铺名称',
  `is_shouhou` int(11) NOT NULL DEFAULT '2' COMMENT '是否售后 1.售后 2.未售后 3.售后完成',
  `uid` int(11) NOT NULL COMMENT '用户id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=104 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
