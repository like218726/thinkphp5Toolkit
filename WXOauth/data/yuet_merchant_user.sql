CREATE TABLE `yuet_merchant_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户ID',
  `user_nick` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `wx_nickname` varchar(150) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '微信昵称',
  `wx_head_pic` varchar(200) NOT NULL DEFAULT '' COMMENT '微信头像',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `type` smallint(4) NOT NULL DEFAULT '0' COMMENT '类型: 0.非会员,1.会员',
  `sex` smallint(4) NOT NULL DEFAULT '0' COMMENT '性别:0.男,1.女',
  `bing_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '绑定手机号',
  `bing_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '绑定手机时间',
  `last_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后登陆时间',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `is_first_buy` smallint(4) NOT NULL DEFAULT '0' COMMENT '首次购买:0.是,1.否',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_nick` (`user_nick`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

