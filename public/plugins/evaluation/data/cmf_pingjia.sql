/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-11-18 09:50:09
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_pingjia
-- ----------------------------
DROP TABLE IF EXISTS `cmf_pingjia`;
CREATE TABLE `cmf_pingjia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productid` int(11) NOT NULL COMMENT '商品id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `score` varchar(10) COLLATE utf8_esperanto_ci NOT NULL COMMENT '评分',
  `comment` text COLLATE utf8_esperanto_ci NOT NULL COMMENT '评论',
  `status` int(2) NOT NULL COMMENT '是否匿名（0不匿名）',
  `orderid` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '订单id',
  `thumb` text COLLATE utf8_esperanto_ci NOT NULL COMMENT '评论缩略图',
  `time` int(15) NOT NULL,
  `nickname` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '用户昵称',
  `avatar` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '用户头像',
  `specid_name` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '商品规格',
  `is_pingtai` int(11) NOT NULL COMMENT '是否平台添加1.平台 2.用户',
  `goods_name` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '商品名称',
  `goods_num` int(11) NOT NULL COMMENT '商品数量',
  `goods_img` varchar(255) COLLATE utf8_esperanto_ci NOT NULL COMMENT '商品主图',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci;
-- ----------------------------
-- Records of cmf_pingjia
-- ----------------------------
