#这是用来生成接口用户的Token
1. 如果用户不存在,则调用此接口
2. url : xxx.com/Bulidtoken/getAccessToken
   param: param[member_id],param[app_id],param[timestamp],param[signature] 其中member_id是用户ID,