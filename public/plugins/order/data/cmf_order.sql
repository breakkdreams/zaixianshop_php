-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-07 02:47:45
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
-- 表的结构 `cmf_order`
--

CREATE TABLE IF NOT EXISTS `cmf_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordersn` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '订单编号',
  `shopid` int(11) NOT NULL COMMENT '商品id',
  `storeid` int(11) NOT NULL COMMENT '店铺id',
  `buycarid` int(11) NOT NULL COMMENT '购物车id',
  `status` int(11) NOT NULL COMMENT '状态 1:待支付 2:待发货  3:待收货 4:待评价 5:已完成 6:已删除',
  `prestatus` int(11) NOT NULL COMMENT '上一次操作状态',
  `shstatus` int(11) NOT NULL COMMENT '售后1：同意退款 2：拒绝退款 3：客服介入 4:退款待处理',
  `uid` int(11) NOT NULL COMMENT '购买用户id',
  `lx_mobile` char(15) NOT NULL COMMENT '联系电话',
  `lx_name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '联系人',
  `lx_code` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '邮编',
  `province` char(20) CHARACTER SET utf8 NOT NULL COMMENT '收货地址_省',
  `city` char(20) CHARACTER SET utf8 NOT NULL COMMENT '收货地址_市',
  `area` char(30) CHARACTER SET utf8 NOT NULL COMMENT '收货地址_区',
  `address` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '收货地址_详细地址',
  `area_code` varchar(20) DEFAULT NULL COMMENT '地区编码',
  `addtime` int(18) NOT NULL COMMENT '添加时间',
  `paytime` int(11) NOT NULL COMMENT '支付时间',
  `fhtime` int(11) NOT NULL COMMENT '发货时间',
  `shipper_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '快递名称',
  `shipper_code` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '快递公司编号',
  `logistics_order` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '物流单号',
  `freeship` int(11) NOT NULL COMMENT '免邮 1.免 2。不免',
  `freight` int(11) NOT NULL COMMENT '运费',
  `totalprice` decimal(11,2) NOT NULL COMMENT '总价',
  `pay_type` int(11) NOT NULL COMMENT '支付方式 1:支付宝 2:微信 3:银行卡 4.余额',
  `usernote` text CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `remind` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '提醒',
  `tk_reason` text NOT NULL COMMENT '退款理由',
  `tk_explain` text NOT NULL,
  `refuse_reason` text NOT NULL COMMENT '拒绝理由',
  `is_del` int(2) DEFAULT '2' COMMENT '是否删除 1.删除  2.未删除',
  `dianpu_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '店铺名称',
  `shouhou_time` int(11) NOT NULL COMMENT '售后时间',
  `shouhuo_time` int(11) NOT NULL COMMENT '收货时间',
  `dianpu_fen` varchar(11) DEFAULT NULL COMMENT '店铺评分',
  `is_shouhou` int(11) NOT NULL COMMENT '是否售后 1.退款中 2.退款成功',
  `is_pingjia` int(11) NOT NULL COMMENT '是否评价 1.评价',
  `transaction_id` varchar(255) NOT NULL COMMENT '交易单号',
  `user_name` varchar(100) NOT NULL COMMENT '用户昵称',
  `sh_delayed` int(11) DEFAULT NULL COMMENT '收货延时',
  `leixing` tinyint(1) DEFAULT NULL COMMENT '订单类型 1.商品 2.拼团 3.砍价',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=102 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
