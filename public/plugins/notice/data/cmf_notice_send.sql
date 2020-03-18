-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2019-12-18 06:50:29
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";




--
-- Database: `shop`
--

-- --------------------------------------------------------

--
-- 表的结构 `cmf_notice_send`
--

CREATE TABLE IF NOT EXISTS `cmf_notice_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `username` varchar(50) DEFAULT NULL COMMENT '用户账号',
  `mobile` char(11) DEFAULT NULL COMMENT '手机号',
  `content` text COMMENT '发送内容',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `nickname` varchar(50) DEFAULT NULL COMMENT '用户昵称',
  `status` int(11) NOT NULL COMMENT '1单发2群发',
  `type` int(11) DEFAULT NULL COMMENT '类型1站内信2短信3邮箱4APP5小程序6公众号',
  `send_time` int(11) NOT NULL COMMENT '发送时间',
  `sendname` varchar(50) DEFAULT '系统消息' COMMENT '发件人',
  `recipients` varchar(100) DEFAULT NULL COMMENT '收件人',
  `de_id` longtext NOT NULL,
  `u_read` longtext NOT NULL COMMENT '未读',
  `thumb` varchar(255) NOT NULL DEFAULT '/plugins/notice/view/public/assets/images/message.png' COMMENT '缩略图',
  `url` varchar(255) DEFAULT NULL COMMENT '跳转链接',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `is_success` int(11) NOT NULL COMMENT '1发送成功2发送失败',
  `comm_type` int(11) NOT NULL COMMENT '公众号消息类型1客服消息2模板消息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息推送表' AUTO_INCREMENT=62 ;

