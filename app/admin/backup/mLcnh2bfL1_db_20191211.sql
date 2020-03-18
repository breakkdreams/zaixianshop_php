# zy bakfile
# version:ThinkCMF 5.0
# time:2019-12-11 11:40:59
# type:zy
# website:http://www.300c.cn
# --------------------------------------------------------


DROP TABLE IF EXISTS `cmf_assess`;
CREATE TABLE `cmf_assess` (
  `assess_id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `personal_leave` int(11) DEFAULT NULL,
  `late` int(11) DEFAULT NULL,
  `abs` int(11) DEFAULT NULL,
  `sick_leave` int(11) DEFAULT NULL,
  `ear` int(11) DEFAULT NULL,
  `fraud` int(11) DEFAULT NULL,
  `cknl` varchar(255) DEFAULT NULL,
  `wcgz` varchar(255) DEFAULT NULL,
  `zxlh` varchar(255) DEFAULT NULL,
  `btl` varchar(255) DEFAULT NULL,
  `dota` int(11) DEFAULT NULL,
  `ywsp` int(11) DEFAULT NULL,
  `team` int(11) DEFAULT NULL,
  `report` int(11) DEFAULT NULL,
  `represe` int(11) DEFAULT NULL,
  `wanc` int(11) DEFAULT NULL,
  `zhil` int(11) DEFAULT NULL,
  `zj` int(11) DEFAULT NULL,
  `zjl` int(11) DEFAULT NULL,
  `commit` text,
  `time` char(11) NOT NULL,
  `zjpy` varchar(255) DEFAULT NULL,
  `zjlpy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`assess_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_asset`;
CREATE TABLE `cmf_asset` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小,单位B',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:可用,0:不可用',
  `download_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `file_key` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件惟一码',
  `filename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `file_path` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件路径,相对于upload目录,可以为url',
  `file_md5` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件md5值',
  `file_sha1` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `suffix` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀名,不包括点',
  `more` text COMMENT '其它详细信息,JSON格式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='资源表';


DROP TABLE IF EXISTS `cmf_auth_access`;
CREATE TABLE `cmf_auth_access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类,请加应用前缀,如admin_',
  `company` int(10) NOT NULL COMMENT '公司id',
  `menu_id` int(20) NOT NULL COMMENT '原始菜单id',
  `access_versions_id` int(11) NOT NULL COMMENT '菜单版本id',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COMMENT='权限授权表';

INSERT INTO `cmf_auth_access` VALUES('61','2','admin/Plugin/default','0','0','1','0');
INSERT INTO `cmf_auth_access` VALUES('62','2','admin/Hook/index','1','0','2','0');
INSERT INTO `cmf_auth_access` VALUES('63','2','admin/Hook/plugins','1','0','3','0');
INSERT INTO `cmf_auth_access` VALUES('64','2','admin/Hook/pluginListOrder','2','0','4','0');
INSERT INTO `cmf_auth_access` VALUES('65','2','admin/Hook/sync','1','0','5','0');
INSERT INTO `cmf_auth_access` VALUES('66','2','admin/Setting/default','0','0','6','0');
INSERT INTO `cmf_auth_access` VALUES('67','2','admin/Plugin/index','1','0','42','0');
INSERT INTO `cmf_auth_access` VALUES('68','2','admin/Plugin/toggle','2','0','43','0');
INSERT INTO `cmf_auth_access` VALUES('69','2','admin/Plugin/setting','1','0','44','0');
INSERT INTO `cmf_auth_access` VALUES('70','2','admin/Plugin/settingPost','2','0','45','0');
INSERT INTO `cmf_auth_access` VALUES('71','2','admin/Plugin/install','2','0','46','0');
INSERT INTO `cmf_auth_access` VALUES('72','2','admin/Plugin/update','2','0','47','0');
INSERT INTO `cmf_auth_access` VALUES('73','2','admin/Plugin/uninstall','2','0','48','0');
INSERT INTO `cmf_auth_access` VALUES('74','2','admin/User/default','0','0','49','0');
INSERT INTO `cmf_auth_access` VALUES('75','2','admin/Rbac/index','1','0','50','0');
INSERT INTO `cmf_auth_access` VALUES('76','2','admin/Rbac/ajaxAdd','2','0','51','0');
INSERT INTO `cmf_auth_access` VALUES('77','2','admin/Rbac/roleAddPost','2','0','52','0');
INSERT INTO `cmf_auth_access` VALUES('78','2','admin/Rbac/ajaxEdit','2','0','53','0');
INSERT INTO `cmf_auth_access` VALUES('79','2','admin/Rbac/ajaxEditPost','2','0','54','0');
INSERT INTO `cmf_auth_access` VALUES('80','2','admin/Rbac/ajaxDelete','2','0','55','0');
INSERT INTO `cmf_auth_access` VALUES('81','2','admin/Rbac/ajaxAuthorize','2','0','56','0');
INSERT INTO `cmf_auth_access` VALUES('82','2','admin/Setting/password','1','0','73','0');
INSERT INTO `cmf_auth_access` VALUES('83','2','admin/Setting/passwordPost','2','0','74','0');
INSERT INTO `cmf_auth_access` VALUES('84','2','user/AdminIndex/default','0','0','109','0');
INSERT INTO `cmf_auth_access` VALUES('85','2','admin/User/index','1','0','110','0');
INSERT INTO `cmf_auth_access` VALUES('86','2','admin/User/add','1','0','111','0');
INSERT INTO `cmf_auth_access` VALUES('87','2','admin/User/addPost','2','0','112','0');
INSERT INTO `cmf_auth_access` VALUES('88','2','admin/User/edit','1','0','113','0');
INSERT INTO `cmf_auth_access` VALUES('89','2','admin/User/editPost','2','0','114','0');
INSERT INTO `cmf_auth_access` VALUES('90','2','admin/User/userInfo','1','0','115','0');
INSERT INTO `cmf_auth_access` VALUES('91','2','admin/User/userInfoPost','2','0','116','0');
INSERT INTO `cmf_auth_access` VALUES('92','2','admin/User/delete','2','0','117','0');
INSERT INTO `cmf_auth_access` VALUES('93','2','user/AdminIndex/default1','0','0','152','0');
INSERT INTO `cmf_auth_access` VALUES('94','2','user/AdminIndex/index','1','0','153','0');
INSERT INTO `cmf_auth_access` VALUES('95','2','user/AdminIndex/ban','2','0','154','0');
INSERT INTO `cmf_auth_access` VALUES('96','2','user/AdminIndex/cancelBan','2','0','155','0');
INSERT INTO `cmf_auth_access` VALUES('97','2','user/AdminOauth/index','1','0','156','0');
INSERT INTO `cmf_auth_access` VALUES('98','2','user/AdminOauth/delete','2','0','157','0');
INSERT INTO `cmf_auth_access` VALUES('99','2','admin/department/index','1','0','162','0');
INSERT INTO `cmf_auth_access` VALUES('100','2','admin/Department/ajaxAdd','2','0','165','0');
INSERT INTO `cmf_auth_access` VALUES('101','2','admin/Department/ajaxEdit','2','0','166','0');
INSERT INTO `cmf_auth_access` VALUES('102','2','admin/department/ajaxEditPost','2','0','167','0');
INSERT INTO `cmf_auth_access` VALUES('103','2','admin/Department/ajaxDelete','2','0','168','0');
INSERT INTO `cmf_auth_access` VALUES('104','2','admin/Rbac/roleAdd','1','0','182','0');
INSERT INTO `cmf_auth_access` VALUES('105','2','admin/Rbac/roleEdit','1','0','183','0');
INSERT INTO `cmf_auth_access` VALUES('106','2','admin/Rbac/roleEditPost','2','0','184','0');
INSERT INTO `cmf_auth_access` VALUES('107','2','admin/Rbac/roleDelete','2','0','185','0');
INSERT INTO `cmf_auth_access` VALUES('108','2','admin/Rbac/authorize','1','0','186','0');
INSERT INTO `cmf_auth_access` VALUES('109','2','admin/Rbac/authorizePost','2','0','187','0');
INSERT INTO `cmf_auth_access` VALUES('110','2','admin/User/erwei','2','0','201','0');
INSERT INTO `cmf_auth_access` VALUES('111','2','admin/Update/index','1','0','440','0');
INSERT INTO `cmf_auth_access` VALUES('112','1','admin/Plugin/default','0','35','1','5');
INSERT INTO `cmf_auth_access` VALUES('113','1','admin/Setting/default','0','35','6','5');
INSERT INTO `cmf_auth_access` VALUES('114','1','admin/Plugin/index','1','35','42','5');
INSERT INTO `cmf_auth_access` VALUES('115','1','admin/Plugin/toggle','2','35','43','5');
INSERT INTO `cmf_auth_access` VALUES('116','1','admin/Plugin/setting','1','35','44','5');
INSERT INTO `cmf_auth_access` VALUES('117','1','admin/Plugin/settingPost','2','35','45','5');
INSERT INTO `cmf_auth_access` VALUES('118','1','admin/Plugin/install','2','35','46','5');
INSERT INTO `cmf_auth_access` VALUES('119','1','admin/Plugin/update','2','35','47','5');
INSERT INTO `cmf_auth_access` VALUES('120','1','admin/Plugin/uninstall','2','35','48','5');
INSERT INTO `cmf_auth_access` VALUES('121','1','plugin/ModuleConfig/TableConfig/clearTable','0','35','391','5');
INSERT INTO `cmf_auth_access` VALUES('122','1','plugin/ModuleConfig/VisitConfig/deleteConfig','1','35','396','5');
INSERT INTO `cmf_auth_access` VALUES('123','1','plugin/ModuleConfig/TableConfig/updateFile','0','35','392','5');
INSERT INTO `cmf_auth_access` VALUES('124','1','plugin/ModuleConfig/VisitConfig/index','1','35','393','5');
INSERT INTO `cmf_auth_access` VALUES('125','1','plugin/ModuleConfig/AdminIndex/index','1','35','387','5');
INSERT INTO `cmf_auth_access` VALUES('126','1','plugin/ModuleConfig/DemandConfig/index','1','35','388','5');
INSERT INTO `cmf_auth_access` VALUES('127','1','plugin/ModuleConfig/DemandConfig/updateDemandConfigData','0','35','389','5');
INSERT INTO `cmf_auth_access` VALUES('128','1','plugin/ModuleConfig/TableConfig/index','1','35','390','5');
INSERT INTO `cmf_auth_access` VALUES('129','1','admin/Update/index','1','35','440','5');

DROP TABLE IF EXISTS `cmf_auth_rule`;
CREATE TABLE `cmf_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `app` varchar(15) NOT NULL COMMENT '规则所属module',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(100) NOT NULL DEFAULT '' COMMENT '额外url参数',
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则描述',
  `condition` varchar(200) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `module` (`app`,`status`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='权限规则表';


DROP TABLE IF EXISTS `cmf_backup_record`;
CREATE TABLE `cmf_backup_record` (
  `id` smallint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '客户名称',
  `data_ip` char(20) NOT NULL COMMENT '文件名称',
  `data_user` varchar(60) NOT NULL COMMENT '路径',
  `data_password` varchar(64) NOT NULL COMMENT '类型',
  `create_time` int(11) NOT NULL COMMENT '备份时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='数据备份记录表';


DROP TABLE IF EXISTS `cmf_business_card`;
CREATE TABLE `cmf_business_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int(11) NOT NULL COMMENT '用户id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `modify_time` int(11) NOT NULL COMMENT '修改时间',
  `content` text NOT NULL COMMENT '内容',
  `sort` int(1) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_company`;
CREATE TABLE `cmf_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公司id',
  `company_name` varchar(255) NOT NULL COMMENT '公司名称',
  `company_num` varchar(255) NOT NULL COMMENT '公司组织代码',
  `parent_id` int(11) NOT NULL COMMENT '上级管理id',
  `status` int(1) NOT NULL COMMENT '状态;0:禁用,1:正常,2:未验证审核中，3:未通过审核',
  `company_type` int(3) NOT NULL COMMENT '所属行业',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `versions_type` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `address` varchar(255) NOT NULL COMMENT '公司地址',
  `tel` varchar(20) NOT NULL COMMENT '公司电话',
  `url` varchar(255) NOT NULL COMMENT '网址',
  `remark` text NOT NULL COMMENT '备注',
  `company_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `super_admin` int(11) NOT NULL COMMENT '超管id',
  `super_login` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='公司表';

INSERT INTO `cmf_company` VALUES('1','卓远网络子系统开发账号','2019','8','1','8','1573004092','5','台州市椒江区君悦大厦B幢1232-1237室','05760000000','www.300c.cn','申请通过！ 处理时间：2019年11月06日 09时35分00秒','35','198','1','subsystem');

DROP TABLE IF EXISTS `cmf_demo`;
CREATE TABLE `cmf_demo` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL COMMENT '名称',
  `create_time` char(11) NOT NULL COMMENT '添加时间',
  `islock` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `nickname` varchar(100) NOT NULL COMMENT '昵称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='例子表';


DROP TABLE IF EXISTS `cmf_department`;
CREATE TABLE `cmf_department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_NO` varchar(2) DEFAULT NULL COMMENT '部门编号 ',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未激活，1已激活',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '部门副ID',
  `list_order` int(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '部门名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `company_id` int(11) NOT NULL COMMENT '公司id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_No` (`department_NO`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `cmf_department` VALUES('1','10','1','0','0','办公室','办公室','1');

DROP TABLE IF EXISTS `cmf_hook`;
CREATE TABLE `cmf_hook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '钩子类型(1:系统钩子;2:应用钩子;3:模板钩子)',
  `once` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只允许一个插件运行(0:多个;1:一个)',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子',
  `app` varchar(15) NOT NULL DEFAULT '' COMMENT '应用名(只有应用钩子才用)',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子表';


DROP TABLE IF EXISTS `cmf_hook_plugin`;
CREATE TABLE `cmf_hook_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名',
  `plugin` varchar(30) NOT NULL DEFAULT '' COMMENT '插件',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子插件表';


DROP TABLE IF EXISTS `cmf_link`;
CREATE TABLE `cmf_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:不显示',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接地址',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '友情链接名称',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '友情链接图标',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `rel` varchar(50) NOT NULL DEFAULT '' COMMENT '链接与网站的关系',
  PRIMARY KEY (`id`),
  KEY `link_visible` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='友情链接表';


DROP TABLE IF EXISTS `cmf_module_audit_log`;
CREATE TABLE `cmf_module_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT '内容',
  `add_time` int(11) NOT NULL COMMENT '时间',
  `issue_id` int(11) NOT NULL COMMENT '发送人id',
  `issue` varchar(30) NOT NULL COMMENT '发送人',
  `module_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT '模板id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COMMENT='模块审核日志表';

INSERT INTO `cmf_module_audit_log` VALUES('1','提交审核成功，请等待！','1571032883','1','卓远','5','0');
INSERT INTO `cmf_module_audit_log` VALUES('2','提交审核成功，请等待！','1571100815','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('3','提交审核成功，请等待！','1571128349','1','卓远','7','0');
INSERT INTO `cmf_module_audit_log` VALUES('4','提交审核成功，请等待！','1571186197','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('5','提交审核成功，请等待！','1571187024','1','卓远','7','0');
INSERT INTO `cmf_module_audit_log` VALUES('6','提交审核成功，请等待！','1571187157','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('7','&lt;p&gt;sdfsdfsedf&lt;/p&gt;','1571203356','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('8','&lt;p&gt;sdfsdf&lt;/p&gt;','1571203401','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('9','&lt;p&gt;&lt;img src=&quot;/upload/20191016/1725b185918b61669a9ac8c2c83bccef.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;','1571203462','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('10','&lt;p&gt;&lt;img src=&quot;/upload/20191016/1725b185918b61669a9ac8c2c83bccef.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;','1571203469','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('11','&lt;p&gt;&lt;img src=&quot;/upload/20191016/1725b185918b61669a9ac8c2c83bccef.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;','1571203500','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('12','提交审核成功，请等待！','1571203847','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('13','提交审核成功，请等待！','1571204120','1','卓远','7','0');
INSERT INTO `cmf_module_audit_log` VALUES('14','提交审核成功，请等待！','1571204420','1','卓远','12','0');
INSERT INTO `cmf_module_audit_log` VALUES('15','&lt;p&gt;跟着流程走&lt;/p&gt;','1571204435','1','卓远','12','0');
INSERT INTO `cmf_module_audit_log` VALUES('16','提交审核成功，请等待！','1571204466','1','卓远','12','0');
INSERT INTO `cmf_module_audit_log` VALUES('17','提交审核成功，请等待！','1571204979','1','卓远','12','0');
INSERT INTO `cmf_module_audit_log` VALUES('18','审核通过。','1571205185','1','卓远','12','0');
INSERT INTO `cmf_module_audit_log` VALUES('19','提交审核成功，请等待！','1571205409','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('20','审核通过。','1571205416','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('21','提交审核成功，请等待！','1571205556','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('22','审核通过。','1571205570','1','卓远','11','0');
INSERT INTO `cmf_module_audit_log` VALUES('23','提交审核成功，请等待！','1571207616','1','卓远','13','0');
INSERT INTO `cmf_module_audit_log` VALUES('24','审核通过。','1571207626','1','卓远','13','0');
INSERT INTO `cmf_module_audit_log` VALUES('25','提交审核成功，请等待！','1571209556','1','卓远','13','0');
INSERT INTO `cmf_module_audit_log` VALUES('26','提交审核成功，请等待！','1571210219','1','卓远','13','0');
INSERT INTO `cmf_module_audit_log` VALUES('27','&lt;p&gt;&lt;img src=&quot;/upload/20191016/68cd61e5f722cf485ec88fdb2efae770.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;','1571210243','1','卓远','13','0');
INSERT INTO `cmf_module_audit_log` VALUES('28','提交审核成功，请等待！','1571214087','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('29','&lt;p&gt;sdfdsf&lt;/p&gt;','1571214101','1','卓远','9','0');
INSERT INTO `cmf_module_audit_log` VALUES('30','提交审核成功，请等待！','1571273872','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('31','&lt;p&gt;模块一步申请驳回测试&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/upload/20191017/b4b494b9980df8d96b403379d9d0859a.jpg&quot; title=&quot;bckimg.jpg&quot; alt=&quot;bckimg.jpg&quot;/&gt;&lt;/p&gt;','1571273980','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('32','提交审核成功，请等待！','1571274071','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('33','审核通过。','1571274078','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('34','提交审核成功，请等待！','1571274133','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('35','&lt;p&gt;模块申请第二次驳回测试&lt;/p&gt;&lt;p style=&quot;line-height: 16px;&quot;&gt;&lt;img style=&quot;vertical-align: middle; margin-right: 2px;&quot; src=&quot;http://csgl.300c.cn/static/js/ueditor/dialogs/attachment/fileTypeImages/icon_txt.gif&quot;/&gt;&lt;a style=&quot;font-size:12px; color:#0066cc;&quot; href=&quot;/upload/20191017/4c7c52e0ae2baf2e84424baf593e3f91.xmind&quot; title=&quot;江湖令（TP系统）.xmind&quot;&gt;江湖令（TP系统）.xmind&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1571274215','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('36','提交审核成功，请等待！','1571274429','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('37','审核通过。','1571274435','1','卓远','14','0');
INSERT INTO `cmf_module_audit_log` VALUES('38','提交审核成功，请等待！','1571283555','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('39','&lt;p&gt;驳回测试&lt;/p&gt;','1571283574','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('40','提交审核成功，请等待！','1571283601','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('41','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>拒绝理由：</div>&lt;p&gt;测试驳回&lt;/p&gt;','1571283635','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('42','提交审核成功，请等待！','1571283884','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('43','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>拒绝理由：</div>&lt;p&gt;测试驳回&lt;/p&gt;','1571283934','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('44','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571283945','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('45','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571283956','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('46','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571283990','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('47','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>拒绝理由：</div>&lt;p&gt;200&lt;/p&gt;','1571284134','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('48','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571284157','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('49','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1571284190','1','卓远','15','0');
INSERT INTO `cmf_module_audit_log` VALUES('50','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571296801','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('51','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;sfdsvcsdvsdvcdcdsc&lt;/p&gt;','1571297499','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('52','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571297526','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('53','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571297534','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('54','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571297575','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('55','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;将信息补充完整&lt;/p&gt;','1571297595','1','卓远','16','0');
INSERT INTO `cmf_module_audit_log` VALUES('56','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571365209','1','卓远','17','0');
INSERT INTO `cmf_module_audit_log` VALUES('57','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571365220','1','卓远','17','0');
INSERT INTO `cmf_module_audit_log` VALUES('58','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571365447','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('59','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;adasdasd&lt;/p&gt;','1571365476','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('60','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571365525','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('61','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571365556','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('62','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571365680','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('63','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1571365691','1','卓远','18','0');
INSERT INTO `cmf_module_audit_log` VALUES('64','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571365762','1','卓远','17','0');
INSERT INTO `cmf_module_audit_log` VALUES('65','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1571365767','1','卓远','17','0');
INSERT INTO `cmf_module_audit_log` VALUES('66','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571388261','1','卓远','19','0');
INSERT INTO `cmf_module_audit_log` VALUES('67','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571388283','1','卓远','19','0');
INSERT INTO `cmf_module_audit_log` VALUES('68','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571388321','1','卓远','19','0');
INSERT INTO `cmf_module_audit_log` VALUES('69','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1571388331','1','卓远','19','0');
INSERT INTO `cmf_module_audit_log` VALUES('70','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571620454','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('71','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571620461','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('72','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571640627','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('73','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571715542','195','项腾飞','21','0');
INSERT INTO `cmf_module_audit_log` VALUES('74','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;反对法&lt;/p&gt;','1571724287','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('75','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571730152','195','项腾飞','22','0');
INSERT INTO `cmf_module_audit_log` VALUES('76','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571730159','195','项腾飞','22','0');
INSERT INTO `cmf_module_audit_log` VALUES('77','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571802034','1','卓远','1','0');
INSERT INTO `cmf_module_audit_log` VALUES('78','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571802080','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('79','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571809827','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('80','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571815468','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('81','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1571816890','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('82','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571818668','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('83','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1571818921','1','卓远','20','0');
INSERT INTO `cmf_module_audit_log` VALUES('84','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1572072553','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('85','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;123&lt;br/&gt;&lt;/p&gt;','1572072644','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('86','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1572072708','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('87','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1572072733','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('88','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1572073003','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('89','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1572073044','195','项腾飞','23','0');
INSERT INTO `cmf_module_audit_log` VALUES('90','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1572934088','195','项腾飞','25','0');
INSERT INTO `cmf_module_audit_log` VALUES('91','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1572934099','195','项腾飞','25','0');
INSERT INTO `cmf_module_audit_log` VALUES('92','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573021766','1','卓远','26','0');
INSERT INTO `cmf_module_audit_log` VALUES('93','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573449010','195','项腾飞','27','0');
INSERT INTO `cmf_module_audit_log` VALUES('94','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573450529','203','苏媛媛','28','0');
INSERT INTO `cmf_module_audit_log` VALUES('95','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573451057','1','卓远','30','0');
INSERT INTO `cmf_module_audit_log` VALUES('96','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;模块简介,需要介绍模块功能&amp;nbsp;&amp;nbsp;&lt;/p&gt;&lt;p&gt;流程图url 地址不正确&amp;nbsp; 文件未上传&lt;/p&gt;&lt;p&gt;导图地址不正确&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;文件未上传&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1573451272','1','卓远','28','0');
INSERT INTO `cmf_module_audit_log` VALUES('97','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573451578','203','苏媛媛','28','0');
INSERT INTO `cmf_module_audit_log` VALUES('98','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;after_sale&lt;/p&gt;','1573454105','1','卓远','27','0');
INSERT INTO `cmf_module_audit_log` VALUES('99','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573530740','204','王浩力','31','0');
INSERT INTO `cmf_module_audit_log` VALUES('100','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573540844','205','谭智文','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('101','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573542376','203','苏媛媛','29','0');
INSERT INTO `cmf_module_audit_log` VALUES('102','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;0&lt;/p&gt;','1573543732','1','卓远','29','0');
INSERT INTO `cmf_module_audit_log` VALUES('103','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573543946','203','苏媛媛','29','0');
INSERT INTO `cmf_module_audit_log` VALUES('104','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573543961','1','卓远','29','0');
INSERT INTO `cmf_module_audit_log` VALUES('105','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573607901','206','颜鹏杰','33','0');
INSERT INTO `cmf_module_audit_log` VALUES('106','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573608961','207','徐敏健','34','0');
INSERT INTO `cmf_module_audit_log` VALUES('107','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573609832','193','叶洋洋','33','0');
INSERT INTO `cmf_module_audit_log` VALUES('108','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573609837','193','叶洋洋','34','0');
INSERT INTO `cmf_module_audit_log` VALUES('109','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;流程图URL中内容不规范。&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;模块简介未填写。&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;模块标识要求首字母大写。&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; text-align: right; background-color: rgb(255, 255, 255);&quot;&gt;模块标识要求规范易懂。&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;','1573610642','193','叶洋洋','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('110','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573612602','205','谭智文','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('111','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573790044','211','赵健淇','35','0');
INSERT INTO `cmf_module_audit_log` VALUES('112','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573790118','1','卓远','35','0');
INSERT INTO `cmf_module_audit_log` VALUES('113','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573796544','194','罗先豪','36','0');
INSERT INTO `cmf_module_audit_log` VALUES('114','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;设计图地址需要放到lct服务器,并且要以制定格式存放&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/upload/20191115/a29cbe44c07e58345b86d5e5900d77d2.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;参考文档: https:oa.300c.cn 文档管理中如下图:&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/upload/20191115/93adeca677a5e41ff6b12b9b7bffe67c.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;注意:&lt;/p&gt;&lt;p&gt;思维导图请导出为图片存放&lt;/p&gt;&lt;p&gt;流程图请将rp发布为html存放&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;文件上传ftp:&amp;nbsp;&amp;nbsp;&lt;/p&gt;&lt;p&gt;地址:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;47.96.24.12&lt;br/&gt;账号:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;gllct&lt;br/&gt;密码:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;t28p3n2d&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;请互相转告.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1573797468','1','卓远','31','0');
INSERT INTO `cmf_module_audit_log` VALUES('115','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p style=&quot;white-space: normal;&quot;&gt;设计图地址需要放到lct服务器,并且要以制定格式存放&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;img src=&quot;https://gl.300c.cn/upload/20191115/a29cbe44c07e58345b86d5e5900d77d2.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;参考文档: https:oa.300c.cn 文档管理中如下图:&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;img src=&quot;https://gl.300c.cn/upload/20191115/93adeca677a5e41ff6b12b9b7bffe67c.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;注意:&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;思维导图请导出为图片存放&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;流程图请将rp发布为html存放&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;文件上传ftp:&amp;nbsp;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;地址:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;47.96.24.12&lt;br/&gt;账号:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;gllct&lt;br/&gt;密码:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;t28p3n2d&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;请互相转告.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1573797479','1','卓远','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('116','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573798460','193','叶洋洋','36','0');
INSERT INTO `cmf_module_audit_log` VALUES('117','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573801249','205','谭智文','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('118','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573802090','194','罗先豪','36','0');
INSERT INTO `cmf_module_audit_log` VALUES('119','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;错误哦&lt;/p&gt;','1573807318','193','叶洋洋','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('120','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1573807339','193','叶洋洋','36','0');
INSERT INTO `cmf_module_audit_log` VALUES('121','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;设计图地址需要放到lct服务器,并且要以制定格式存放&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;img src=&quot;https://gl.300c.cn/upload/20191115/a29cbe44c07e58345b86d5e5900d77d2.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot; style=&quot;box-sizing: border-box; display: inline-block; vertical-align: middle; border: 0px; max-width: 100%;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;参考文档: https:oa.300c.cn 文档管理中如下图:&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;img src=&quot;https://gl.300c.cn/upload/20191115/93adeca677a5e41ff6b12b9b7bffe67c.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot; style=&quot;box-sizing: border-box; display: inline-block; vertical-align: middle; border: 0px; max-width: 100%;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;注意:&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;思维导图请导出为图片存放&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;流程图请将rp发布为html存放&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;文件上传ftp:&amp;nbsp;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;地址:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;47.96.24.12&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;账号:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;gllct&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;密码:&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp;t28p3n2d&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;请互相转告.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1573807499','193','叶洋洋','21','0');
INSERT INTO `cmf_module_audit_log` VALUES('122','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573807657','205','谭智文','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('123','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;红包不够充分，补充一下&lt;/p&gt;','1573866630','193','叶洋洋','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('124','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1573871208','205','谭智文','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('125','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1573880972','193','叶洋洋','32','0');
INSERT INTO `cmf_module_audit_log` VALUES('126','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574057885','206','颜鹏杰','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('127','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574061680','212','王博迪','41','0');
INSERT INTO `cmf_module_audit_log` VALUES('128','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574064003','208','陈益','42','0');
INSERT INTO `cmf_module_audit_log` VALUES('129','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574073180','211','赵健淇','43','0');
INSERT INTO `cmf_module_audit_log` VALUES('130','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574083438','193','叶洋洋','43','0');
INSERT INTO `cmf_module_audit_log` VALUES('131','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;&lt;br/&gt;前后台，不要把这个功能忘记放上去了。在原型图上表示出来即可&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/upload/20191118/8e88c4c5ccf29b226aa2c190187c1a8e.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;','1574084028','193','叶洋洋','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('132','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;1、拓扑图,不够细。&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2、原型图不能截图页面，需要用条条框框画出来。&lt;/p&gt;&lt;p&gt;截图那个页面的是属于ui图的部分&lt;/p&gt;','1574084830','193','叶洋洋','41','0');
INSERT INTO `cmf_module_audit_log` VALUES('133','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;1、拓扑图,不够细。&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;2、原型图不能截图页面，需要用条条框框画出来。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 10px; padding: 0px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); color: rgb(44, 62, 80); font-family: &amp;quot;Source Sans Pro&amp;quot;, Calibri, Candara, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;截图那个页面的是属于ui图的部分&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1574086251','193','叶洋洋','42','0');
INSERT INTO `cmf_module_audit_log` VALUES('134','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574128616','206','颜鹏杰','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('135','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;123&lt;/p&gt;','1574128903','193','叶洋洋','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('136','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574128947','206','颜鹏杰','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('137','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574129156','193','叶洋洋','37','0');
INSERT INTO `cmf_module_audit_log` VALUES('138','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574141137','211','赵健淇','44','0');
INSERT INTO `cmf_module_audit_log` VALUES('139','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p style=&quot;white-space: normal;&quot;&gt;&lt;img src=&quot;https://gl.300c.cn/upload/20191119/888b845a7233261ea5c06fde3f816bdb.png&quot; title=&quot;image.png&quot; alt=&quot;image.png&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;white-space: normal;&quot;&gt;流程图地址有问题，无法访问&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;','1574143587','193','叶洋洋','44','0');
INSERT INTO `cmf_module_audit_log` VALUES('140','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574152819','212','王博迪','41','0');
INSERT INTO `cmf_module_audit_log` VALUES('141','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574153781','212','王博迪','45','0');
INSERT INTO `cmf_module_audit_log` VALUES('142','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574159762','211','赵健淇','44','0');
INSERT INTO `cmf_module_audit_log` VALUES('143','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574212525','194','罗先豪','46','0');
INSERT INTO `cmf_module_audit_log` VALUES('144','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574214100','204','王浩力','31','0');
INSERT INTO `cmf_module_audit_log` VALUES('145','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574227160','211','赵健淇','43','0');
INSERT INTO `cmf_module_audit_log` VALUES('146','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574391433','214','张建杭','47','0');
INSERT INTO `cmf_module_audit_log` VALUES('147','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574393153','195','项腾飞','48','0');
INSERT INTO `cmf_module_audit_log` VALUES('148','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574393523','193','叶洋洋','48','0');
INSERT INTO `cmf_module_audit_log` VALUES('149','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;原型图不够细，没有备注，原型图是要求能点击的，可以参考项腾飞的刷单2号原型图。&lt;/p&gt;&lt;p&gt;要求能用原型图把流程走通，如果需要其他模块配合的，请标注清楚。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;思维导图在列细一点，如拼单列表，可以手动加入其他人进行拼团等&lt;/p&gt;','1574405769','193','叶洋洋','47','0');
INSERT INTO `cmf_module_audit_log` VALUES('150','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574671832','211','赵健淇','35','0');
INSERT INTO `cmf_module_audit_log` VALUES('151','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574672005','193','叶洋洋','41','0');
INSERT INTO `cmf_module_audit_log` VALUES('152','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574738974','211','赵健淇','49','0');
INSERT INTO `cmf_module_audit_log` VALUES('153','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574740091','211','赵健淇','50','0');
INSERT INTO `cmf_module_audit_log` VALUES('154','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574747409','1','卓远','44','0');
INSERT INTO `cmf_module_audit_log` VALUES('155','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程二审核通过','1574747459','1','卓远','43','0');
INSERT INTO `cmf_module_audit_log` VALUES('156','<span style="color:green;" class="glyphicon glyphicon-ok"></span>流程一审核通过','1574747464','1','卓远','46','0');
INSERT INTO `cmf_module_audit_log` VALUES('157','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;有问题&lt;/p&gt;','1574748238','1','卓远','35','0');
INSERT INTO `cmf_module_audit_log` VALUES('158','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574755755','194','罗先豪','51','0');
INSERT INTO `cmf_module_audit_log` VALUES('159','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574755938','194','罗先豪','52','0');
INSERT INTO `cmf_module_audit_log` VALUES('160','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574756045','194','罗先豪','53','0');
INSERT INTO `cmf_module_audit_log` VALUES('161','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574757092','194','罗先豪','54','0');
INSERT INTO `cmf_module_audit_log` VALUES('162','f''d''f','0','1','卓远','0','5');
INSERT INTO `cmf_module_audit_log` VALUES('163','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574902066','1','admin','0','1');
INSERT INTO `cmf_module_audit_log` VALUES('164','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574903009','1','admin','0','1');
INSERT INTO `cmf_module_audit_log` VALUES('165','<span style="color:blue;" class="glyphicon glyphicon-ok"></span> 审核通过!','1574907059','1','admin','0','1');
INSERT INTO `cmf_module_audit_log` VALUES('166','<span style="color:blue;" class="glyphicon glyphicon-ok"></span> 审核通过!','1574907069','1','admin','0','13');
INSERT INTO `cmf_module_audit_log` VALUES('167','<span style="color:blue;" class="glyphicon glyphicon-ok"></span> 审核通过!','1574907163','1','admin','0','15');
INSERT INTO `cmf_module_audit_log` VALUES('168','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574907319','1','admin','0','1');
INSERT INTO `cmf_module_audit_log` VALUES('169','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574907547','1','admin','0','1');
INSERT INTO `cmf_module_audit_log` VALUES('170','<span style="color:blue;" class="glyphicon glyphicon-ok"></span> 审核通过!','1574908956','1','admin','0','5');
INSERT INTO `cmf_module_audit_log` VALUES('171','<div><span style="color:red;" class="glyphicon glyphicon-remove"></span>审核失败，拒绝理由：</div>&lt;p&gt;sdfsdfsdfdf&lt;/p&gt;','1574911183','1','admin','0','4');
INSERT INTO `cmf_module_audit_log` VALUES('172','<span style="color:blue;" class="glyphicon glyphicon-refresh"></span>提交审核成功，请等待！','1574912803','1','admin','0','1');

DROP TABLE IF EXISTS `cmf_module_category`;
CREATE TABLE `cmf_module_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '分类名称',
  `parent_id` int(11) DEFAULT '0' COMMENT '父级id',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='模块分类';

INSERT INTO `cmf_module_category` VALUES('1','商城','0','1');
INSERT INTO `cmf_module_category` VALUES('2','管理','0','1');
INSERT INTO `cmf_module_category` VALUES('5','公共','0','1');

DROP TABLE IF EXISTS `cmf_module_config`;
CREATE TABLE `cmf_module_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `module` varchar(50) NOT NULL COMMENT '本模块名',
  `module_name` varchar(50) NOT NULL COMMENT '模块名称',
  `need_module` varchar(50) NOT NULL COMMENT '所需模块名称',
  `need_module_name` varchar(50) NOT NULL COMMENT '所需模块名称',
  `name` varchar(50) NOT NULL COMMENT '配置名称',
  `api` varchar(300) NOT NULL COMMENT 'api地址',
  `supply_api` varchar(300) NOT NULL COMMENT '供应api',
  `keywords` varchar(25) NOT NULL COMMENT '关键词  模块名+自增id',
  `need` text NOT NULL COMMENT '需求文档',
  `supply` text NOT NULL COMMENT '供应文档',
  `type` int(1) NOT NULL COMMENT '发布类型 0需求  1供应',
  `issuer` varchar(25) NOT NULL COMMENT '发布人id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `class` varchar(255) DEFAULT NULL COMMENT '类引用地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='模块配置表保存模块需要的api 文档说明等信息 异常重要';


DROP TABLE IF EXISTS `cmf_module_manage`;
CREATE TABLE `cmf_module_manage` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `module_name` varchar(30) NOT NULL COMMENT '模块名称 标识',
  `table_name` varchar(30) NOT NULL COMMENT '数据表明',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_module_purchase_history`;
CREATE TABLE `cmf_module_purchase_history` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `module_id` int(11) NOT NULL COMMENT '模块id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `user_name` varchar(30) NOT NULL COMMENT '用户名',
  `expire` int(11) NOT NULL COMMENT '到期时间',
  `payment_amount` decimal(8,5) NOT NULL COMMENT '付款金额',
  `payment_type` tinyint(1) NOT NULL COMMENT '1支付宝  2微信 ',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `operate_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型 0、1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='模块购买记录表';

INSERT INTO `cmf_module_purchase_history` VALUES('6','8','17','1','卓远','0','0.00000','0','1572332426','1');

DROP TABLE IF EXISTS `cmf_module_store`;
CREATE TABLE `cmf_module_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '模块名',
  `title` varchar(20) NOT NULL COMMENT '模块中文名',
  `describe` text NOT NULL COMMENT '描述',
  `version` varchar(10) NOT NULL COMMENT '版本',
  `author` varchar(20) NOT NULL COMMENT '开发者姓名',
  `author_id` int(11) NOT NULL COMMENT '开发者id',
  `introduce` text NOT NULL COMMENT '产品介绍',
  `warning` varchar(255) NOT NULL COMMENT '温馨提示',
  `price` decimal(8,5) DEFAULT '0.00000' COMMENT '价格',
  `preview` text COMMENT '预览图',
  `mind_map` varchar(255) DEFAULT NULL COMMENT '思维导图文件地址',
  `mind_map_url` varchar(255) DEFAULT NULL COMMENT '思维导图url',
  `flow_chart` varchar(255) DEFAULT NULL COMMENT '流程图',
  `flow_chart_url` varchar(255) DEFAULT NULL COMMENT '流程图url',
  `ui` varchar(255) DEFAULT NULL COMMENT 'ui图',
  `ui_url` varchar(255) DEFAULT NULL COMMENT 'ui图url',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `putway` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上架  0下架 1上架',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '当前流程',
  `resource` varchar(300) DEFAULT NULL COMMENT '模块源文件',
  `category` int(11) NOT NULL COMMENT '模块分类id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COMMENT='模块市场表';

INSERT INTO `cmf_module_store` VALUES('16','ceshi','ceshi','','1.0','','0','','','0.00000','','','s234','','234234','','234324','','3','0','2','','1');
INSERT INTO `cmf_module_store` VALUES('17','AliPay','支付宝支付模块','支付宝支付模块','1.0','卓远','1','支付宝支付模块','温馨提醒','999.99999','upload/module/preview/20191018102915713657602285a06ae.png','','http://','','http://','','http://','1571365175','4','0','2','upload/module/resource/2019101810291571365757f8f7d68.zip','2');
INSERT INTO `cmf_module_store` VALUES('18','WechatPay','微信支付','微信支付','1.0','卓远','1','dsads','fdsfdsf','0.00000','upload/module/preview/201910181027157136564391b3311.jpg,upload/module/preview/201910181027157136564333117e.png','','http://','','http://','','http://','1571365444','4','0','2','upload/module/resource/201910181027157136566911d4afd.zip','2');
INSERT INTO `cmf_module_store` VALUES('21','BrushingMerchant','刷单商家','刷单商家页面','1.0','项腾飞','195','','','0.00000','','','https://oa.300c.cn/admin/index/index.html','','http://lct.300c.cn/刷单','','https://oa.300c.cn/admin/index/index.html','1571715537','2','0','1','','2');
INSERT INTO `cmf_module_store` VALUES('22','Invitation','邀请模块','通过链接发送邀请并建立','1.0','项腾飞','195','','','0.00000','','','http://lct.300c.cn/刷单','','http://lct.300c.cn/刷单','','http://lct.300c.cn/刷单','1571730148','3','0','2','','1');
INSERT INTO `cmf_module_store` VALUES('23','Exam','考试管理','这个一个考试管理，里面有试卷、评分...','1.0','项腾飞','195','在线考试系统功能模块介绍 - 在线考试系统功能模块介绍 模块 功能说明 用户管理 1、用户包括用户 ID、用户名、邮件、注册 IP、积分、角色、注册时间等...','此模块必须要包含会员模块和评分模块','0.00000','upload/module\exam\1.0\preview\1427df3.jpg','upload/module\exam\1.0\mindMap\shuidaitong-0969c5b995c6ab15434fa159b9c7a791dc8b14b2.zip','http://lct.300c.cn/tyshop','upload/module\exam\1.0\flowChart\jsencrypt-master.zip','http://lct.300c.cn/tyshop','upload/module\exam\1.0\uiMap\jsencrypt-master.zip','http://lct.300c.cn/tyshop','1572072490','4','0','2','upload/module\exam\1.0\resource\JS-RSA.zip','5');
INSERT INTO `cmf_module_store` VALUES('24','Alisms','阿里云短信','用阿里云发短信','1.0','项腾飞','195','','','0.00000','','','http://lct.300c.cn/zyshop/#g=1&amp;p=提现_1','','http://lct.300c.cn/zyshop/#g=1&amp;p=','','','1572080629','0','0','0','','5');
INSERT INTO `cmf_module_store` VALUES('25','Fund','资金模块','资金操作','1.0','项腾飞','195','','','0.00000','','upload/module\fund\1.0\mindMap\资金模块.zip','http://lct.300c.cn/钱包/资金模块.png','upload/module\fund\1.0\flowChart\钱包.zip','http://lct.300c.cn/钱包','','','1572934025','3','0','2','','1');
INSERT INTO `cmf_module_store` VALUES('27','Sale','售后模块','售后服务及内容','1.0','项腾飞','195','','','0.00000','','upload/module\sale\1.0\mindMap\sale.rar','http://lct.300c.cn/刷单','upload/module\sale\1.0\flowChart\sale.rar','http://lct.300c.cn/刷单','','','1573448995','0','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('29','Recharge','充值模块','实习-账户的充值','1.0','苏媛媛','203','','','0.00000','','upload/module\recharges\1.0\mindMap\1.zip','0','upload/module\recharges\1.0\flowChart\1.zip','0','','','1573450167','3','0','2','','5');
INSERT INTO `cmf_module_store` VALUES('31','FileManage','附件模块','附件模块(图片，视频，其他文件)','1.0','王浩力','204','','','0.00000','','upload/module\file_manage\1.0\mindMap\Desktop.zip','http://lct.6xq.cn/module/FileManage/mindmap/1.0/2019111901.png','upload/module\file_manage\1.0\flowChart\2019112001.zip','http://lct.6xq.cn/module/FileManage/flowchat/1.0/2019112001','','','1573530095','1','0','1','','5');
INSERT INTO `cmf_module_store` VALUES('32','DiscountCoupon','红包优惠券模块','红包及优惠券','1.0','谭智文','205','','','0.00000','','upload/module\discount_coupon\1.0\mindMap\红包优惠券模块思维导图.zip','http://lct.6xq.cn/module/DiscountCoupon/mindmap/1.0/2019111514.png','upload/module\discount_coupon\1.0\flowChart\优惠券001.rar','http://lct.6xq.cn/module/DiscountCoupon/flowchat/1.0/2019111514/#id=vsfkxd&amp;p=%E5%85%91%E6%8D%A2%E4%BC%98%E6%83%A0%E5%88%B8_%E5%89%8D_','','','1573540670','3','0','2','','1');
INSERT INTO `cmf_module_store` VALUES('33','Wallet','账号管理','钱包模块:账户管理，提现，充值','1.0','颜鹏杰','206','','','0.00000','','upload/module\wallet\1.0\mindMap\新建文件夹.rar','0','upload/module\wallet\1.0\flowChart\新建文件夹.rar','0','','','1573607841','3','0','2','','5');
INSERT INTO `cmf_module_store` VALUES('34','Withdraw','提现模块','钱包提现功能模块（实习）','1.0','徐敏健','207','','','0.00000','','upload/module\withdraw\1.0\mindMap\新建文件夹.zip','0','upload/module\withdraw\1.0\flowChart\新建文件夹.zip','0','','','1573608871','3','0','2','','5');
INSERT INTO `cmf_module_store` VALUES('35','Answer','答题模块','答题模块-后台添加题目 给前台提供各种api','1.0','赵健淇','211','','','0.00000','','upload/module\answer\1.0\mindMap\答题模块思维导图.zip','http://lct.6xq.cn/module/Answer/flowchat/1.0/2019111501.png','upload/module\answer\1.0\flowChart\答题模块流程图.zip','http://lct.6xq.cn/module/Answer/mindmap/1.0/2019111501/index.html#id=dk01q6&amp;p=%E9%A6%96%E9%A1%B5','','','1573781745','3','0','2','','5');
INSERT INTO `cmf_module_store` VALUES('1','AdminJournal','操作日志','操作日志','1.0','罗先豪','194','记录后台和前台和接口的所有控制器方法访问信息','删除模块的时候请先卸载模块，如果没执行卸载就删除模块，可能会报错，解决方法：找到hook_plugin表 删除对应的模块数据','0.00000','upload/module\admin_journal\1.0\preview\f1dc3465.png','upload/module\admin_journal\1.0\mindMap\操作日志模块.zip','http://lct.6xq.cn/module/admin_journal/flowchat/1.0/2019111501.png','upload/module\admin_journal\1.0\flowChart\操作日志-原型图.zip','http://lct.6xq.cn/module/admin_journal/mindmap/1.0/2019111501/#id=t0yjkf&amp;p=%E9%A6%96%E9%A1%B5&amp;g=1','upload/module\admin_journal\1.0\uiMap\操作日志-ui.zip','http://lct.6xq.cn/module/admin_journal/ui/1.0/2019111501/#id=t0yjkf&amp;p=ui&amp;g=1','1573796152','4','1','2','upload/module\admin_journal\1.0\resource\日志模块.zip','5');
INSERT INTO `cmf_module_store` VALUES('37','Vote','投票模块','投票模块：添加活动，查看活动等','1.0','颜鹏杰','206','','','0.00000','','upload/module\vote\1.0\mindMap\思维导图.rar','http://lct.6xq.cn//module/Vote/mindmap/1.0/2019111801/%E6%8A%95%E7%A5%A8%E6%A8%A1%E5%9D%97.png','upload/module\vote\1.0\flowChart\原型图流程图.rar','http://lct.6xq.cn/module/Vote/flowchat/1.0/2019111801/toupiao/index.html#id=xffc0c&amp;p=正进行中&amp;g=1','','','1574057780','3','0','2','','5');
INSERT INTO `cmf_module_store` VALUES('40','sdfsd','sdf','sdf','1.0.1','卓远','1','','','0.00000','','','sdf','','sdf','','','1574061494','0','0','0','','1');
INSERT INTO `cmf_module_store` VALUES('41','Freight','运费模块','运费模板','1.0.2','王博迪','212','','','0.00000','','upload/module\freight\1.0\mindMap\运费模块.xmind.zip','http://lct.6xq.cn/module/Freight/flowchat/1.0/2019111801.png','upload/module\freight\1.0\flowChart\运费模板.rp.zip','http://lct.6xq.cn/module/Freight/mindmap/1.0/2019111901/index.html','','','1574061605','1','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('42','Goods','商品模块','商品管理','1.0','陈益','208','','','0.00000','','upload/module\goods\1.0\mindMap\goods.zip','http://lct.6xq.cn/module/goods/mindmap/1.1/2019111801/index.html','upload/module\goods\1.0\flowChart\商品模块.zip','http://lct.6xq.cn/module/goods/flowchat/1.1/goods.png','','','1574063896','2','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('43','SiteConfiguration','站点配置','站点配置模块 ','1.0','赵健淇','211','站点配置模块 ','站点配置信息','0.00000','upload/module\site_configuration\1.0\preview\ddb0ad5c60.png,upload/module\site_configuration\1.0\preview\12a2ca.png','upload/module\site_configuration\1.0\mindMap\站点配置模块思维图.zip','http://lct.6xq.cn/module/SiteConfiguration/flowchat/1.01/2019111801.png','upload/module\site_configuration\1.0\flowChart\站点配置模块流程图.zip','http://lct.6xq.cn/module/SiteConfiguration/mindmap/1.01/2019111801/index.html#id=sdh3u5&amp;p=%E7%AB%99%E7%82%B9%E9%85%8D%E7%BD%AE-seo%E8%AE%BE%E7%BD%AE','upload/module\site_configuration\1.0\uiMap\站点配置模块-UI.zip','http://lct.6xq.cn/module/SiteConfiguration/ui/1.01/2019111801/index.html','1574073079','1','0','2','upload/module\site_configuration\1.0\resource\站点配置模块.zip','5');
INSERT INTO `cmf_module_store` VALUES('44','MemberAddress','地址管理','后台管理地址模块 -管理用户收货地址信息','1.00','赵健淇','211','','','0.00000','','upload/module\member_address\1.0\mindMap\地址模块思维导图.zip','http://lct.6xq.cn/module/MemberAddress/flowchat/1.00/2019111801.png','upload/module\member_address\1.0\flowChart\地址模块流程图.zip','http://lct.6xq.cn/module/MemberAddress/mindmap/1.00/2019111801/index.html','','','1574141042','1','0','1','','5');
INSERT INTO `cmf_module_store` VALUES('45','Relation','用户关系模块','分销奖励模块','1.0','王博迪','212','','','0.00000','','upload/module\relation\1.0\mindMap\分销.xmind.zip','http://lct.6xq.cn/module/Relation/flowchat/1.0/2019111901.png','upload/module\relation\1.0\flowChart\分销模块.rp.zip','http://lct.6xq.cn/module/Relation/mindmap/1.0/index.html','','','1574153757','1','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('46','ExpressKdniao','快递','快递配置和物流查询','1.0','罗先豪','194','','','0.00000','','upload/module\express_kdniao\1.0\mindMap\快递.zip','http://lct.6xq.cn/module/express_kdniao/flowchat/1.0/快递鸟.png','upload/module\express_kdniao\1.0\flowChart\快递.zip','http://lct.6xq.cn/module/express_kdniao/mindmap/1.0/2019112001','','','1574212387','1','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('47','SpellList','拼单模块','购买商品时使用','1.0','张建杭','214','','','0.00000','','upload/module\spell_list\1.0\mindMap\拼单模块.rar','http://lct.6xq.cn/module/SpellList/mindmap/1.0/2019111801/%E6%8B%BC%E5%8D%95%E6%A8%A1%E5%9D%97.png','upload/module\spell_list\1.0\flowChart\拼单模块.rar','http://lct.6xq.cn/module/SpellList/flowchat/1.0/2019112201/pindan/index.html#id=5ugtqc&amp;p=拼单&amp;g=1','','','1574389471','2','0','1','','1');
INSERT INTO `cmf_module_store` VALUES('48','Feedback','用户反馈','csa','1.0','项腾飞','195','','','0.00000','','upload/module\feedback\1.0\mindMap\feedback.zip','http://tp.300c.cn','upload/module\feedback\1.0\flowChart\feedback.zip','http://tp.300c.cn','','','1574393138','3','0','2','','1');

DROP TABLE IF EXISTS `cmf_module_temp_framework`;
CREATE TABLE `cmf_module_temp_framework` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL COMMENT '框架名称',
  `src` varchar(255) NOT NULL COMMENT '保存路径',
  `add_time` int(11) NOT NULL COMMENT '保存时间',
  `user_id` int(11) NOT NULL,
  `user_name` varchar(25) NOT NULL COMMENT '用户名称',
  `versions` char(8) DEFAULT NULL COMMENT '版本号',
  `describe` varchar(255) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='前端模板框架';

INSERT INTO `cmf_module_temp_framework` VALUES('1','测试框架vue3.0','','1574385992','1','卓远','3','Vue.js（读音 /vjuː/, 类似于 view） 是一套构建用户界面的渐进式框架。

Vue 只关注视图层， 采用自底向上增量开发的设计。

Vue 的目标是通过尽可能简单的 API 实现响应的数据绑定和组合的视图组件。

Vue 学习起来非常简单，本教程基于 Vue 2.1.8 版本测试。

');
INSERT INTO `cmf_module_temp_framework` VALUES('20','vue3.1','upload/templateFramework\\dist(2).rar','1574761992','1','admin','3.1','发射点发的');

DROP TABLE IF EXISTS `cmf_module_template`;
CREATE TABLE `cmf_module_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(30) NOT NULL COMMENT '模板名称',
  `module_store_id` int(11) NOT NULL COMMENT '应用商店模块id',
  `framework_id` varchar(100) NOT NULL COMMENT '框架版本id',
  `name` varchar(30) NOT NULL COMMENT '模块名',
  `title` varchar(20) NOT NULL COMMENT '模块中文名',
  `describe` text NOT NULL COMMENT '描述',
  `author` varchar(20) NOT NULL COMMENT '开发者姓名',
  `author_id` int(11) NOT NULL COMMENT '开发者id',
  `introduce` text NOT NULL COMMENT '产品介绍',
  `warning` varchar(255) NOT NULL COMMENT '温馨提示',
  `price` decimal(8,5) DEFAULT '0.00000' COMMENT '价格',
  `preview` text COMMENT '预览图',
  `mind_map` varchar(255) DEFAULT NULL COMMENT '思维导图文件地址',
  `mind_map_url` varchar(255) DEFAULT NULL COMMENT '思维导图url',
  `flow_chart` varchar(255) DEFAULT NULL COMMENT '流程图',
  `flow_chart_url` varchar(255) DEFAULT NULL COMMENT '流程图url',
  `ui` varchar(255) DEFAULT NULL COMMENT 'ui图',
  `ui_url` varchar(255) DEFAULT NULL COMMENT 'ui图url',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态 0未提交 1审核中 2审核失败  3通过',
  `putway` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上架  0下架 1上架',
  `step` int(11) NOT NULL DEFAULT '0' COMMENT '当前流程',
  `resource` varchar(300) DEFAULT NULL COMMENT '模板源文件',
  `version` varchar(10) NOT NULL COMMENT '模块版本',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='模块模板表';

INSERT INTO `cmf_module_template` VALUES('1','商品测试模块','42','1','Goods','商品模块','sfded','卓远','1','s''d''f','s''d''f','0.00000','','','','','','','','','3','0','0','/upload/module/goods/1.0/template/goods.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('4','售后模板','27','1','Withdraw','提现模块','d''f','卓远','1','s''d''f','dfsdf','0.00000','upload/module\sale\1.0\template\preview\7e232ef.jpg','upload/module\sale\1.0\template\mindMap\update_20191112_1.0.1.zip','sdf','upload/module\sale\1.0\template\flowChart\update_20191112_1.0.1.zip','sdf','upload/module\sale\1.0\template\uiMap\update_20191112_1.0.1.zip','sdf','','0','0','0','/upload/module/sale/1.0/template/order.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('5','地址模块','44','1','Recharge','充值模块','sd','卓远','1','似懂非懂','胜多负少的','0.00000','upload/module\member_address\1.0\template\preview\ee63f.jpg','upload/module\member_address\1.0\template\mindMap\update_20191112_1.0.1.zip',' dsfsdf','upload/module\member_address\1.0\template\flowChart\update_20191112_1.0.1.zip','sdfsd','upload/module\member_address\1.0\template\uiMap\update_20191112_1.0.1.zip','sdfs','','3','0','0','upload/module\member_address\1.0\template\update_20191112_1.0.1.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('12','sdfsdf','0','','Recharge','充值模块','sdfdf','','0','sdfdsf','sdfsdf','0.00000','','','sdfdsf','','sdfdsf','','sdfsdf','','0','0','0','','1.0');
INSERT INTO `cmf_module_template` VALUES('13','sdfsdf','0','','Recharge','充值模块','sdfdf','','0','sdfdsf','sdfsdf','0.00000','','upload/module\recharge\1.0\template\mindMap\update_20191112_1.0.1.zip','sdfdsf','upload/module\recharge\1.0\template\flowChart\update_20191112_1.0.1.zip','sdfdsf','upload/module\recharge\1.0\template\uiMap\update_20191112_1.0.1.zip','sdfsdf','','3','0','0','upload/module\recharge\1.0\template\update_20191112_1.0.1.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('14','sdfsdf','0','','Recharge','充值模块','sdfdf','','0','sdfd','dsfdsf','0.00000','','','sdfdf','','sdf','','sdf','','0','0','0','','1.0');
INSERT INTO `cmf_module_template` VALUES('16','sfd','0','','DiscountCoupon','红包优惠券模块','sdf','','0','sdf','sdf','0.00000','','','sdf','','sdf','','sdf','','0','0','0','','1.0');
INSERT INTO `cmf_module_template` VALUES('17','sfd','0','','DiscountCoupon','红包优惠券模块','sdf','','0','sdf','sdf','0.00000','','','sdf','','sdf','','sdf','','0','0','0','','1.0');
INSERT INTO `cmf_module_template` VALUES('18','sfd','0','','DiscountCoupon','红包优惠券模块','sdf','','0','sdf','sdf','0.00000','','upload/module\discount_coupon\1.0\template\mindMap\update_20191112_1.0.1.zip','sdf','upload/module\discount_coupon\1.0\template\flowChart\update_20191112_1.0.1.zip','sdf','upload/module\discount_coupon\1.0\template\uiMap\update_20191112_1.0.1.zip','sdf','','0','0','0','upload/module\discount_coupon\1.0\template\update_20191112_1.0.1.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('19','地址模块','0','','Wallet','账号管理','sd','','0','30','333','0.00000','','','222','','efsdfds','','sdfsdf','','0','0','0','/upload/module/member_address/1.0/template/address.zip','1.0');
INSERT INTO `cmf_module_template` VALUES('20','ddfsdf','0','','DiscountCoupon','红包优惠券模块','sdfsd','admin','1','fsdfsdf','dsfsdf','0.00000','upload/module\discount_coupon\1.0\template\preview\384ad9b1d.jpg','upload/module\discount_coupon\1.0\template\mindMap\update_20191112_1.0.1.zip','sdfsdf','upload/module\discount_coupon\1.0\template\flowChart\update_20191112_1.0.1.zip','sdf','upload/module\discount_coupon\1.0\template\uiMap\update_20191112_1.0.1.zip','sdf','1574912761','1','0','0','upload/module\discount_coupon\1.0\template\update_20191112_1.0.1.zip','1.0');

DROP TABLE IF EXISTS `cmf_module_template_config`;
CREATE TABLE `cmf_module_template_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(30) NOT NULL COMMENT '模块标识',
  `name` varchar(30) NOT NULL COMMENT '模块名字',
  `version` varchar(10) NOT NULL COMMENT '版本',
  `explain` varchar(255) NOT NULL COMMENT '说明',
  `address` varchar(300) NOT NULL COMMENT '地址',
  `demand_symbol` varchar(30) DEFAULT NULL COMMENT '所需模块标识',
  `demand_name` varchar(30) DEFAULT NULL COMMENT '所需模块名字',
  `type` tinyint(1) NOT NULL COMMENT '0 供应配置  1 需求配置',
  `keywords` varchar(30) DEFAULT NULL COMMENT '关键字',
  `detail` text NOT NULL COMMENT '细则',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='模块模板信息配置表';

INSERT INTO `cmf_module_template_config` VALUES('22','Wallet','账号管理','1.0','跳转到/order_list 并传参goodsid num addressid','/order_list','Recharge','充值模块','1','Wallet_order_list','');
INSERT INTO `cmf_module_template_config` VALUES('23','Recharge','充值模块','1.0','跳转到/address_list 并传参goodsid num','/address_list','Wallet','账号管理','1','Recharge_address_list','');
INSERT INTO `cmf_module_template_config` VALUES('24','Fund','资金模块','1.0','跳转到/order_list 并传参goodsid num','/order_list','Recharge','充值模块','1','Fund_order_list','');
INSERT INTO `cmf_module_template_config` VALUES('26','Recharge','充值模块','1.0','我需要的参数goodsid num addressid','/order_list','','','0','','');
INSERT INTO `cmf_module_template_config` VALUES('27','Wallet','账号管理','1.0','我需要的参数goodsid num','/address_list','','','0','','');
INSERT INTO `cmf_module_template_config` VALUES('28','Fund','资金模块','1.0','我不需要参数,我的地址给别的模块跳转用','/goods_list','','','0','','');
INSERT INTO `cmf_module_template_config` VALUES('29','Recharge','充值模块','1.0','跳转到/goods_list','/goods_list','Fund','资金模块','1','Recharge_shop_list','');

DROP TABLE IF EXISTS `cmf_nav`;
CREATE TABLE `cmf_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_main` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否为主导航;1:是;0:不是',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '导航位置名称',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='前台导航位置表';


DROP TABLE IF EXISTS `cmf_nav_menu`;
CREATE TABLE `cmf_nav_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nav_id` int(11) NOT NULL COMMENT '导航 id',
  `parent_id` int(11) NOT NULL COMMENT '父 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '打开方式',
  `href` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `icon` varchar(20) NOT NULL DEFAULT '' COMMENT '图标',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '层级关系',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='前台导航菜单表';


DROP TABLE IF EXISTS `cmf_option`;
CREATE TABLE `cmf_option` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `autoload` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否自动加载;1:自动加载;0:不自动加载',
  `option_name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置名',
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '配置值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='全站配置表';

INSERT INTO `cmf_option` VALUES('1','1','admin_dashboard_widgets','[{"name":"CmfHub","is_system":1},{"name":"MainContributors","is_system":1},{"name":"Custom2","is_system":1},{"name":"Custom1","is_system":1},{"name":"Contributors","is_system":1},{"name":"Custom3","is_system":1},{"name":"Custom4","is_system":1},{"name":"Custom5","is_system":1}]');

DROP TABLE IF EXISTS `cmf_plugin`;
CREATE TABLE `cmf_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '插件类型;1:网站;8:微信',
  `has_admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理,0:没有;1:有',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:开启;0:禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `hooks` varchar(255) NOT NULL DEFAULT '' COMMENT '实现的钩子;以“,”分隔',
  `author` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `description` varchar(255) NOT NULL COMMENT '插件描述',
  `config` text COMMENT '插件配置',
  `module_menu_id` int(20) NOT NULL COMMENT '原始菜单id',
  `table_name` varchar(255) DEFAULT NULL COMMENT '提醒功能关联的表',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='插件表';

INSERT INTO `cmf_plugin` VALUES('7','1','1','1','0','ModuleConfig','模块配置','','','卓远网络','','1.0','模块配置','[]','387','');
INSERT INTO `cmf_plugin` VALUES('9','1','1','1','0','Demo','模块演示','','','叶洋洋','','1.0','模块演示','[]','342','');
INSERT INTO `cmf_plugin` VALUES('10','1','1','1','0','SubAppmarket','应用市场','','','卓远网络','','1.0','应用市场模块','[]','577','');

DROP TABLE IF EXISTS `cmf_portal_category`;
CREATE TABLE `cmf_portal_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类文章数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `description` varchar(255) NOT NULL COMMENT '分类描述',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `seo_title` varchar(100) NOT NULL DEFAULT '',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '',
  `seo_description` varchar(255) NOT NULL DEFAULT '',
  `list_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类列表模板',
  `one_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类文章页模板',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章分类表';


DROP TABLE IF EXISTS `cmf_portal_category_post`;
CREATE TABLE `cmf_portal_category_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章id',
  `category_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `term_taxonomy_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='portal应用 分类文章对应表';


DROP TABLE IF EXISTS `cmf_portal_post`;
CREATE TABLE `cmf_portal_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `post_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型,1:文章;2:页面',
  `post_format` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '内容格式;1:html;2:md',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '发表者用户id',
  `post_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:已发布;0:未发布;',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论状态;1:允许;0:不允许',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶;1:置顶;0:不置顶',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_hits` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `post_like` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `comment_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `published_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `post_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'post标题',
  `post_keywords` varchar(150) NOT NULL DEFAULT '' COMMENT 'seo keywords',
  `post_excerpt` varchar(500) NOT NULL DEFAULT '' COMMENT 'post摘要',
  `post_source` varchar(150) NOT NULL DEFAULT '' COMMENT '转载文章的来源',
  `post_content` text COMMENT '文章内容',
  `post_content_filtered` text COMMENT '处理过的文章内容',
  `more` text COMMENT '扩展属性,如缩略图;格式为json',
  PRIMARY KEY (`id`),
  KEY `type_status_date` (`post_type`,`post_status`,`create_time`,`id`),
  KEY `post_parent` (`parent_id`),
  KEY `post_author` (`user_id`),
  KEY `post_date` (`create_time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='portal应用 文章表';


DROP TABLE IF EXISTS `cmf_portal_tag`;
CREATE TABLE `cmf_portal_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签文章数',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章标签表';


DROP TABLE IF EXISTS `cmf_portal_tag_post`;
CREATE TABLE `cmf_portal_tag_post` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签 id',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `term_taxonomy_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='portal应用 标签文章对应表';


DROP TABLE IF EXISTS `cmf_receive`;
CREATE TABLE `cmf_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(200) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_recycle_bin`;
CREATE TABLE `cmf_recycle_bin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0' COMMENT '删除内容 id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `table_name` varchar(60) DEFAULT '' COMMENT '删除内容所在表名',
  `name` varchar(255) DEFAULT '' COMMENT '删除内容名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT=' 回收站';


DROP TABLE IF EXISTS `cmf_role`;
CREATE TABLE `cmf_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父角色ID',
  `department_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID；默认为0，未分配',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态;0:禁用;1:正常',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `list_order` float NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `company_id` int(11) DEFAULT NULL COMMENT '公司id',
  PRIMARY KEY (`id`),
  KEY `parentId` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

INSERT INTO `cmf_role` VALUES('1','0','1','1','1573004441','0','0','CEO','CEO','1');

DROP TABLE IF EXISTS `cmf_route`;
CREATE TABLE `cmf_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态;1:启用,0:不启用',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'URL规则类型;1:用户自定义;2:别名添加',
  `full_url` varchar(255) NOT NULL DEFAULT '' COMMENT '完整url',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '实际显示的url',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='url路由表';


DROP TABLE IF EXISTS `cmf_slide`;
CREATE TABLE `cmf_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示,0不显示',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片分类',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分类备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片表';


DROP TABLE IF EXISTS `cmf_slide_item`;
CREATE TABLE `cmf_slide_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slide_id` int(11) NOT NULL DEFAULT '0' COMMENT '幻灯片id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '幻灯片名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片图片',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片链接',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `description` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '幻灯片描述',
  `content` text CHARACTER SET utf8 COMMENT '幻灯片内容',
  `more` text COMMENT '链接打开方式',
  PRIMARY KEY (`id`),
  KEY `slide_cid` (`slide_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片子项表';


DROP TABLE IF EXISTS `cmf_sms_report`;
CREATE TABLE `cmf_sms_report` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) NOT NULL COMMENT '手机',
  `posttime` int(11) NOT NULL COMMENT '提交时间',
  `id_code` varchar(10) NOT NULL,
  `msg` varchar(90) DEFAULT NULL,
  `send_userid` mediumint(8) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `return_id` varchar(30) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_sms_set`;
CREATE TABLE `cmf_sms_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_id` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_superclass`;
CREATE TABLE `cmf_superclass` (
  `super_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `week` varchar(20) NOT NULL,
  `month` varchar(20) NOT NULL,
  `tit` varchar(255) NOT NULL,
  `bz` varchar(255) NOT NULL,
  PRIMARY KEY (`super_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_sx_grade`;
CREATE TABLE `cmf_sx_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引',
  `pf_content` text NOT NULL COMMENT '评分项',
  `py_content` text NOT NULL COMMENT '评语',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `pf_type` int(1) NOT NULL COMMENT '评分类型  0周评  1月评  ',
  `sum` int(11) NOT NULL COMMENT '总分',
  `assess_time` int(11) NOT NULL COMMENT '考核时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工实习评分表';


DROP TABLE IF EXISTS `cmf_theme`;
CREATE TABLE `cmf_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后升级时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模板状态,1:正在使用;0:未使用',
  `is_compiled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为已编译模板',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '主题目录名，用于主题的维一标识',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '主题名称',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '主题版本号',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '主题作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `lang` varchar(10) NOT NULL DEFAULT '' COMMENT '支持语言',
  `keywords` varchar(50) NOT NULL DEFAULT '' COMMENT '主题关键字',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '主题描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_theme_file`;
CREATE TABLE `cmf_theme_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公共的模板文件',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '模板名称',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模板文件名',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
  `file` varchar(50) NOT NULL DEFAULT '' COMMENT '模板文件，相对于模板根目录，如Portal/index.html',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '模板文件描述',
  `more` text COMMENT '模板更多配置,用户自己后台设置的',
  `config_more` text COMMENT '模板更多配置,来源模板的配置文件',
  `draft_more` text COMMENT '模板更多配置,用户临时保存的配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_third_party_user`;
CREATE TABLE `cmf_third_party_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '本站用户id',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'access_token过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:正常;0:禁用',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `third_party` varchar(20) NOT NULL DEFAULT '' COMMENT '第三方惟一码',
  `app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方应用 id',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `access_token` varchar(512) NOT NULL DEFAULT '' COMMENT '第三方授权码',
  `openid` varchar(40) NOT NULL DEFAULT '' COMMENT '第三方用户id',
  `union_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方用户多个产品中的惟一 id,(如:微信平台)',
  `more` text COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='第三方用户表';


DROP TABLE IF EXISTS `cmf_user`;
CREATE TABLE `cmf_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(8) DEFAULT NULL COMMENT '员工编号',
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户类型;1:admin;2:会员',
  `sex` tinyint(2) NOT NULL DEFAULT '0' COMMENT '性别;0:保密,1:男,2:女',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态;0:禁用,1:正常,2:未验证',
  `service` int(1) NOT NULL DEFAULT '0' COMMENT '1公司客服代表  ',
  `user_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) DEFAULT '' COMMENT '登录密码;cmf_password加密',
  `user_nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网址',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `signature` varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `more` text COMMENT '扩展属性',
  `sfz` varchar(200) NOT NULL COMMENT '身份证',
  `address` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '联系地址',
  `sx_time` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '实习时间',
  `rz_time` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '入职时间',
  `fujian` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '附件',
  `jiguan` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '籍贯',
  `xueli` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '学历',
  `byxy` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '毕业学校',
  `zhuanye` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '专业',
  `jjlianxi` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '紧急联系人',
  `bak` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '备份',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '二维码链接地址',
  `yc_time` int(11) DEFAULT NULL COMMENT '延迟入职时间',
  `ry_token` varchar(255) DEFAULT NULL,
  `sign` varchar(255) DEFAULT NULL COMMENT '签名图片',
  `qq` varchar(25) NOT NULL COMMENT '用户qq',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `user_login` (`user_login`),
  KEY `user_nickname` (`user_nickname`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

INSERT INTO `cmf_user` VALUES('1','10001001','1','0','0','1575954669','0','0','1573004092','1','0','admin','###0fbde5b4ffd4bd2560d5c16179ad45d9','','','','','','192.168.1.15','','15858620686','','','','','','','','','','','','','','','','','');

DROP TABLE IF EXISTS `cmf_user_action`;
CREATE TABLE `cmf_user_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  `reward_number` int(11) NOT NULL DEFAULT '0' COMMENT '奖励次数',
  `cycle_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '周期类型;0:不限;1:按天;2:按小时;3:永久',
  `cycle_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '周期时间值',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `app` varchar(50) NOT NULL DEFAULT '' COMMENT '操作所在应用名或插件名等',
  `url` text COMMENT '执行操作的url',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户操作表';


DROP TABLE IF EXISTS `cmf_user_action_log`;
CREATE TABLE `cmf_user_action_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问次数',
  `last_visit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `object` varchar(100) NOT NULL DEFAULT '' COMMENT '访问对象的id,格式:不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作名称;格式:应用名+控制器+操作名,也可自己定义格式只要不发生冲突且惟一;',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user_id`,`object`,`action`),
  KEY `user_object_action_ip` (`user_id`,`object`,`action`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='访问记录表';


DROP TABLE IF EXISTS `cmf_user_attach`;
CREATE TABLE `cmf_user_attach` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `company_id` int(11) NOT NULL COMMENT '公司id',
  `department_id` int(11) NOT NULL COMMENT '部门id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户附表，用于存储公司部门角色id与用户关联';

INSERT INTO `cmf_user_attach` VALUES('1','1','1','1','1');

DROP TABLE IF EXISTS `cmf_user_detail`;
CREATE TABLE `cmf_user_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cmf_user_favorite`;
CREATE TABLE `cmf_user_favorite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '收藏内容的标题',
  `url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '收藏内容的原文地址，不带域名',
  `description` varchar(500) CHARACTER SET utf8 DEFAULT '' COMMENT '收藏内容的描述',
  `table_name` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '收藏实体以前所在表,不带前缀',
  `object_id` int(10) unsigned DEFAULT '0' COMMENT '收藏内容原来的主键id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户收藏表';


DROP TABLE IF EXISTS `cmf_user_login_attempt`;
CREATE TABLE `cmf_user_login_attempt` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login_attempts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试次数',
  `attempt_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试登录时间',
  `locked_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '锁定时间',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户 ip',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '用户账号,手机号,邮箱或用户名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户登录尝试表';


DROP TABLE IF EXISTS `cmf_user_score_log`;
CREATE TABLE `cmf_user_score_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户操作积分等奖励日志表';


DROP TABLE IF EXISTS `cmf_user_token`;
CREATE TABLE `cmf_user_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `token` varchar(64) NOT NULL DEFAULT '' COMMENT 'token',
  `device_type` varchar(10) NOT NULL DEFAULT '' COMMENT '设备类型;mobile,android,iphone,ipad,web,pc,mac,wxapp',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='用户客户端登录 token 表';

INSERT INTO `cmf_user_token` VALUES('2','1','1591506669','1575954669','df926d8559e7ce72d1711040db8976dae28f77dcb1f6532a2d9c4933d0eede11','web');

DROP TABLE IF EXISTS `cmf_verification_code`;
CREATE TABLE `cmf_verification_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当天已经发送成功的次数',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后发送成功时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证码过期时间',
  `code` varchar(8) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '最后发送成功的验证码',
  `account` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号或者邮箱',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='手机邮箱数字验证码表';


