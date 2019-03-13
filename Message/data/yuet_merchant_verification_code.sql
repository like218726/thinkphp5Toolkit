CREATE TABLE `yuet_merchant_verification_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `telphone` varchar(20) NOT NULL DEFAULT '' COMMENT '绑定的手机号',
  `verification_code` varchar(20) NOT NULL DEFAULT '' COMMENT '验证码',
  `is_used` smallint(4) NOT NULL DEFAULT '0' COMMENT '状态: 0.未使用,1.已使用',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ip` varchar(20) NOT NULL DEFAULT '' COMMENT 'IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机验证码';

