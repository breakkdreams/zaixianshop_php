/*
Navicat MySQL Data Transfer

Source Server         : 优惠券红包
Source Server Version : 50614
Source Host           : 192.168.1.88:3306
Source Database       : subsystem2019

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2019-12-06 13:47:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_code_qrcode
-- ----------------------------
DROP TABLE IF EXISTS `cmf_code_qrcode`;
CREATE TABLE `cmf_code_qrcode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `img_url` varchar(255) DEFAULT NULL COMMENT '图片生成的地址',
  `text` varchar(255) DEFAULT NULL COMMENT '二维码内容',
  `create_status` tinyint(1) DEFAULT NULL COMMENT '创建方式0手动1接口',
  `create_time` char(20) DEFAULT NULL COMMENT '生成时间戳',
  `code_name` varchar(255) DEFAULT NULL COMMENT '二维码名称',
  `qrcode_type` tinyint(1) DEFAULT NULL COMMENT '二维码类型0地址1数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_code_qrcode
-- ----------------------------
