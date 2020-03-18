/*
Navicat MySQL Data Transfer

Source Server         : 优惠券红包
Source Server Version : 50614
Source Host           : 192.168.1.88:3306
Source Database       : subsystem2019

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2019-12-06 11:03:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_code_barccode
-- ----------------------------
DROP TABLE IF EXISTS `cmf_code_barccode`;
CREATE TABLE `cmf_code_barccode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `img_url` varchar(255) DEFAULT NULL COMMENT '一维码地址',
  `text` varchar(255) DEFAULT NULL COMMENT '一维码内容',
  `create_status` tinyint(1) DEFAULT NULL COMMENT '创建方式0手动1接口',
  `create_time` char(20) DEFAULT NULL COMMENT '创建时间',
  `code_name` varchar(255) DEFAULT NULL COMMENT '条形码名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_code_barccode
-- ----------------------------
