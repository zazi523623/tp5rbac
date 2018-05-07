# Host: 127.0.0.1  (Version 5.7.9)
# Date: 2017-12-06 17:17:35
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "admin"
#

CREATE TABLE `admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `admin_name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `admin_nickname` varchar(255) DEFAULT NULL COMMENT '管理员昵称',
  `admin_password` char(32) NOT NULL DEFAULT '' COMMENT '登录密码；md5加密',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `email` varchar(100) DEFAULT NULL COMMENT '管理员邮箱',
  `phone` varchar(16) NOT NULL COMMENT '管理员联系方式',
  `register_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `level` tinyint(3) DEFAULT '0' COMMENT '权限组id 1为超级管理员',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `sup_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '直属充值总数',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '下级充值总数',
  `rebate` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '已提现抽成',
  `checklogin` char(32) NOT NULL DEFAULT '' COMMENT '检测账号是否多用户同时登陆',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `admin_name` (`admin_name`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='管理员表';

#
# Data for table "admin"
#

INSERT INTO `admin` VALUES (1,'admin','老子','44209a6a592dea91bcf7d4dd53e47a5a',1,'127.0.0.1',1512551839,'laozi@qq.com','18888888888',NULL,1,0,0.00,0.00,0.00,'oSvOY4TlqETjLjxXb0Bv9bnnR1fHzr6G'),(2,'一级代理1号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',1,'127.0.0.1',1508292421,NULL,'13687954156',1503039870,2,1,0.00,0.00,0.00,'KPwIwjGOYPHHtoTVFPU6zAh6WagaCASy'),(4,'一级代理2号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',2,'127.0.0.1',1503374016,NULL,'13687954155',1503050633,2,1,0.00,0.00,0.00,'ntRCi2FR7mQPVbvZIclK8Wup3w0h9IKw'),(5,'一级代理3号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',2,'',0,NULL,'18687954166',1503050651,2,1,0.00,0.00,0.00,'cENKgIIsAIdATFjfjM2zi5IBu0VdpwhA'),(8,'二级代理2号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',1,'127.0.0.1',1503626629,NULL,'13687954155',1503300476,3,2,0.00,0.00,0.00,'2mU0fajoucC5C9dqnyQpja4BDP0XpHz7'),(9,'一级代理4号',NULL,'66baca28372925020079c562cca26616',1,'',0,NULL,'18681456896',1503301690,2,1,0.00,0.00,0.00,'tTJ1irkzVp0JM8X9FWeiI01jqL15PuhU'),(10,'二级代理3号',NULL,'66baca28372925020079c562cca26616',1,'127.0.0.1',1503626743,NULL,'15936184888',1503302066,3,4,0.00,0.00,0.00,'tFcCfVTpueLLuKD41UNCJ5FFDKkFxpLs'),(11,'一级代理5号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',1,'192.168.2.218',1508305343,NULL,'13687954155',1503372442,2,1,0.00,0.00,0.00,'0pKXwQwrP4GZZURMDYHM55WKPNDTWKEn'),(13,'二级代理1号',NULL,'44209a6a592dea91bcf7d4dd53e47a5a',1,'127.0.0.1',1508223027,NULL,'13699854568',1503559621,3,2,0.00,0.00,0.00,'MSkokBmDjpXGfbTJMESwZ98vQAw7ibAm'),(17,'test','测试a ','44209a6a592dea91bcf7d4dd53e47a5a',1,'127.0.0.1',1512551708,'','13687954155',1512523555,2,1,0.00,0.00,0.00,'xTS6minsezP4yiVtN7ynFe1UVWNoZg8o'),(18,'hehe','呵呵啊','44209a6a592dea91bcf7d4dd53e47a5a',1,'',0,'','13687954155',1512529309,0,17,0.00,0.00,0.00,''),(19,'sefs','sdfsdfsdfsd','44209a6a592dea91bcf7d4dd53e47a5a',1,'',0,'','13666666666',1512549688,0,17,0.00,0.00,0.00,'');

#
# Structure for table "admin_and_role"
#

CREATE TABLE `admin_and_role` (
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限组Id',
  PRIMARY KEY (`admin_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员和权限组关联表';

#
# Data for table "admin_and_role"
#

INSERT INTO `admin_and_role` VALUES (1,1),(2,2),(4,2),(5,2),(8,3),(9,2),(10,3),(13,3),(17,2);

#
# Structure for table "admin_role"
#

CREATE TABLE `admin_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组id',
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '权限组名',
  `rule_ids` varchar(255) DEFAULT NULL COMMENT '权限规则id',
  `role_status` tinyint(3) DEFAULT '1' COMMENT '权限组状态0：删除 1：正常 2： 禁用',
  PRIMARY KEY (`role_id`),
  KEY `admin_rule_ids` (`rule_ids`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='管理员权限组表';

#
# Data for table "admin_role"
#

INSERT INTO `admin_role` VALUES (1,'超级管理员',NULL,1),(2,'普通管理员','10,2,9,8,23',1);

#
# Structure for table "admin_rule"
#

CREATE TABLE `admin_rule` (
  `rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `rule_name` varchar(32) NOT NULL DEFAULT '' COMMENT '规则名称',
  `rule_icon` varchar(100) DEFAULT NULL COMMENT '规则图标',
  `rule_method` varchar(50) NOT NULL DEFAULT '' COMMENT '规则全路径 控制器/方法',
  `rule_pid` int(11) unsigned DEFAULT '0' COMMENT '规则父id',
  `rule_status` tinyint(2) DEFAULT '1' COMMENT '规则状态 0：删除 1：正常 2：禁用',
  `rule_sort` tinyint(3) unsigned DEFAULT '0' COMMENT '规则排序',
  `rule_level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '规则等级 1：主菜单 2：方法 3：行为',
  PRIMARY KEY (`rule_id`),
  KEY `rule_method` (`rule_method`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='管理员规则表';

#
# Data for table "admin_rule"
#

INSERT INTO `admin_rule` VALUES (1,'权限管理','fa-sitemap','Rule',0,1,9,1),(2,'管理员管理','fa-user-secret','Manager',0,1,7,1),(8,'管理员列表',NULL,'Manager/index',2,1,0,2),(9,'添加管理员',NULL,'Manager/add',2,1,0,2),(10,'首页','fa-home','Index/index',0,1,10,1),(11,'权限列表',NULL,'Rule/index',1,1,1,2),(23,'编辑管理员',NULL,'Manager/edit',8,1,0,3),(24,'删除管理员',NULL,'Manager/delete',8,1,0,3),(25,'添加权限',NULL,'Rule/add',11,1,0,3),(26,'编辑权限',NULL,'Rule/edit',11,1,0,3),(27,'删除权限',NULL,'Rule/delete',11,1,0,3),(31,'权限组列表',NULL,'Group/index',1,1,0,2),(32,'添加权限组',NULL,'Group/operate/action/add',31,1,0,3),(33,'编辑权限组',NULL,'Group/operate/action/edit',31,1,0,3),(34,'删除权限组',NULL,'Group/delete',31,1,0,3),(37,'访问授权',NULL,'Group/authorize',31,1,0,3),(41,'成员授权',NULL,'Group/user',31,1,0,3);
