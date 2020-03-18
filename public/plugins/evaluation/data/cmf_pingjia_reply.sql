/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:zysubsystem20190906
Data:2019-11-18 09:50:09
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_pingjia_reply
-- ----------------------------
DROP TABLE IF EXISTS `cmf_pingjia_reply`;
CREATE TABLE `cmf_pingjia_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL COMMENT '评论主表id',
  `reply_uid` int(11) NOT NULL COMMENT '评论者uid',
  `reply_name` varchar(100) NOT NULL COMMENT '评论者名字',
  `reply_avatar` varchar(255) NOT NULL COMMENT '评论者头像',
  `to_uid` int(11) NOT NULL COMMENT '被评论者uid',
  `to_name` varchar(100) NOT NULL COMMENT '被评论者名字',
  `to_avatar` varchar(255) NOT NULL COMMENT '被评论者头像',
  `content` text NOT NULL COMMENT '评论内容',
  `reply_thumb` text NOT NULL COMMENT '图片',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='评论回复表';
-- ----------------------------
-- Records of cmf_pingjia_reply
-- ----------------------------
