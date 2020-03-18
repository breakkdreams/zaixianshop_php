-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2020-01-13 06:37:54
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
-- 表的结构 `cmf_fund_type`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `photo` varchar(255) NOT NULL COMMENT '图片',
  `name` varchar(22) NOT NULL COMMENT '名称',
  `zid` int(11) NOT NULL COMMENT '支付方式id',
  `number` int(1) NOT NULL COMMENT '1为手动  2为自动',
  `type` int(1) NOT NULL COMMENT '1为开 2为关',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `cmf_fund_type`
--

INSERT INTO `cmf_fund_type` (`id`, `photo`, `name`, `zid`, `number`, `type`) VALUES
(1, '/plugins/fund/view/public/upload/zftype/zhifubao.png', '支付宝手动转账', 1, 1, 1),
(2, '/plugins/fund/view/public/upload/zftype/zhifubao.png', '支付宝自动转账', 1, 2, 1),
(3, '/plugins/fund/view/public/upload/zftype/weixin.png', '微信手动转账', 2, 1, 1),
(4, '/plugins/fund/view/public/upload/zftype/weixin.png', '微信自动转账', 2, 2, 1),
(5, '/plugins/fund/view/public/upload/zftype/bank.png', '银行卡手动转账', 3, 1, 2),
(6, '/plugins/fund/view/public/upload/zftype/bank.png', '银行卡自动转账', 3, 2, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
