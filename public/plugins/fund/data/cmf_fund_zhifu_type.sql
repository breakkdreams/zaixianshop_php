-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2020-01-13 06:38:04
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
-- 表的结构 `cmf_fund_zhifu_type`
--

CREATE TABLE IF NOT EXISTS `cmf_fund_zhifu_type` (
  `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `photo` varchar(255) NOT NULL COMMENT '图片',
  `name` varchar(18) NOT NULL COMMENT '名称',
  `type` int(1) NOT NULL COMMENT '1为启用  2为关闭',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `cmf_fund_zhifu_type`
--

INSERT INTO `cmf_fund_zhifu_type` (`tid`, `photo`, `name`, `type`) VALUES
(1, '/plugins/fund/view/public/upload/zftype/zhifubao.png', '支付宝', 1),
(2, '/plugins/fund/view/public/upload/zftype/weixin.png', '微信', 1),
(3, '/plugins/fund/view/public/upload/zftype/bank.png', '银行卡', 2),
(4, '/plugins/fund/view/public/upload/zftype/balance.png', '余额', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
