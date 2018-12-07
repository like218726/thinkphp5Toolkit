CREATE TABLE `tp_error_alarm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_id` int(11) DEFAULT '0' COMMENT '设备ID',
  `member_id` int(11) DEFAULT '0' COMMENT '用户ID',
  `type` smallint(4) DEFAULT '0' COMMENT '类型:1.忘取眼镜报警: 需要在微信端通知用户\r\n2.机械故障报警: 包括断电/死机或其它异常情况出现的故障的统称\r\n3.断水故障: 表示水位低出现的故障的情况',
  `content` varchar(255) DEFAULT '' COMMENT '报警内容',
  `create_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '报警时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='故障报警';
