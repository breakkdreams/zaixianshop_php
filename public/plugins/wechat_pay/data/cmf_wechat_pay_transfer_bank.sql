-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2020-01-10 03:55:41
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
-- 表的结构 `cmf_wechat_pay_transfer_bank`
--

CREATE TABLE IF NOT EXISTS `cmf_wechat_pay_transfer_bank` (
  `name` varchar(100) DEFAULT NULL COMMENT '银行卡名称',
  `id` int(20) DEFAULT NULL COMMENT '银行卡id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `cmf_wechat_pay_transfer_bank`
--

INSERT INTO `cmf_wechat_pay_transfer_bank` (`name`, `id`) VALUES
('工商银行', 1002),
('农业银行', 1005),
('建设银行', 1003),
('中国银行', 1026),
('交通银行', 1020),
('招商银行', 1001),
('邮储银行', 1066),
('民生银行', 1006),
('平安银行', 1010),
('中信银行', 1021),
('浦发银行', 1004),
('兴业银行', 1009),
('光大银行', 1022),
('广发银行', 1027),
('华夏银行', 1025),
('北京银行', 1032),
('宁波银行', 1056),
('北京银行', 4836),
('上海银行', 1024),
('南京银行', 1054),
('长子县融汇村镇银行', 4755),
('长沙银行', 4216),
('浙江泰隆商业银行', 4051),
('中原银行', 4753),
('企业银行（中国）', 4761),
('顺德农商银行', 4036),
('衡水银行', 4752),
('长治银行', 4756),
('大同银行', 4767),
('河南省农村信用社', 4115),
('宁夏黄河农村商业银行', 4150),
('山西省农村信用社', 4156),
('安徽省农村信用社', 4166),
('甘肃省农村信用社', 4157),
('天津农村商业银行', 4153),
('广西壮族自治区农村信用社', 4113),
('陕西省农村信用社', 4108),
('深圳农村商业银行', 4076),
('宁波鄞州农村商业银行', 4052),
('浙江省农村信用社联合社', 4764),
('江苏省农村信用社联合社', 4217),
('江苏紫金农村商业银行股份有限公司', 4072),
('北京中关村银行股份有限公司', 4769),
('星展银行（中国）有限公司', 4778),
('枣庄银行股份有限公司', 4766),
('海口联合农村商业银行股份有限公司', 4758),
('南洋商业银行（中国）有限公司', 4763);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
