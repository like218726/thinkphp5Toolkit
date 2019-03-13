<?php

namespace app\api\controller;

use think\Db;

use app\common\WechatAppPay;

class MembershipOrder extends Base {
	
	/**
	 * 
	 * 创建订单
	 * 
	 */
	public function CreateOrder() {
		if ($this->request->isPost()) {
			$user_name = input('post.user_name', '', 'trim');
			$sex = input('post.sex', '0', 'trim');
			$order_amount = input('post.order_amount', '0', 'trim');
			$fee_amount = input('post.fee_amount', '0', 'trim');
			$membership_card_id = input('post.membership_card_id', '0', 'trim');
			$sub_code = input('post.sub_code', '', 'trim');
			
			if (!is_float_num($order_amount)) {
				return ajaxError("请输入正确的充值金额");
			}
			
			if (!is_float_num($fee_amount)) {
				return ajaxError("请输入正确的赠送金额");
			}
			
			if (!$membership_card_id) {
				return ajaxError("会员卡ID不能为空");
			}
			
			if (!$sub_code) {
				return ajaxError("会员卡子代码不能为空");
			}
			//查询price字段的子代码
			$membership_sub_code_info = model('MembershipCard')->where('price','like', '%'.$sub_code.'%')->find();
			$membership_sub_code_info = empty($membership_sub_code_info) ? array() : $membership_sub_code_info->toArray();
			if (!$membership_sub_code_info) {
				return ajaxError("会员卡子代码非法");
			}			
			
			$user_info = model('User')->where('id', $this->_uid)->find();
			$user_info = empty($user_info) ? array() : $user_info->toArray();
			$order_user_name = '';
			if ($user_info['is_first_buy'] == 0) {
				if (!$user_name) {
					return ajaxError("用户真实姓名不能为空");
				}	
				$order_user_name = $user_name;			
			} else {
				$order_user_name = $user_info['user_name'];
			}
			
			//订单主表
			$order_header['merchant_id'] = $membership_sub_code_info['merchant_id'];
			$order_header['order_no'] = date('YmdHis',time()).rand(1000, 9999);
			$order_header['user_id'] = $this->_uid;
			$order_header['user_nick'] = $user_info['user_nick'];
			$order_header['user_name'] = $order_user_name;
			$order_header['order_amount'] = $order_amount;
			$order_header['create_time'] = date('Y-m-d H:i:s',time());
			
			//订单明细表
			$order_detail['order_no'] = $order_header['order_no'];
			$order_detail['membership_card_id'] = $membership_card_id;
			$order_detail['membership_card_name'] = $membership_sub_code_info['name'];
			$order_detail['sub_code'] = $sub_code;
			$order_detail['order_amount'] = $order_amount;
			$order_detail['fee_amount'] = $fee_amount;
			$order_detail['order_num'] = 1;
			$order_detail['create_time'] = date('Y-m-d H:i:s',time());	

			//订单日志表
			$order_log = array();
			$amount_arr = array();
			array_push($amount_arr, $order_amount);
			array_push($amount_arr, $fee_amount);
			//订单充值日志表
			foreach ($amount_arr as $k=>$v) {
				$order_log[$k]['merchant_id'] = $membership_sub_code_info['merchant_id'];
				$order_log[$k]['membership_id'] = $membership_sub_code_info['id'];
				$order_log[$k]['membership_code'] = $membership_sub_code_info['code'];
				$order_log[$k]['membership_sub_code'] = $sub_code;
				$order_log[$k]['user_id'] = $this->_uid;
				$order_log[$k]['user_nick'] = $user_info['user_nick'];
				if ($user_info['type'] == 0) {
					$order_log[$k]['pay_method'] = 1;
					$order_log[$k]['pay_sex'] = $sex;
				} else {
					$order_log[$k]['pay_method'] = 2;
				}	
				$order_log[$k]['member_order_no'] = $order_header['order_no'];		
				$order_log[$k]['create_time'] = date('Y-m-d H:i:s',time());									
			}		
			Db::startTrans();
			try{
				model('MembershipOrderHeader')->insert($order_header);
				model('MembershipOrderDetail')->insert($order_detail);
				model('MembershipOrderLog')->insertAll($order_log);
				Db::commit();
            	return ajaxSuccess("操作成功", 1 , array('sub_code'=>$sub_code,'order_no'=>$order_header['order_no'],'membership_code'=>$membership_sub_code_info['code']));
			}catch (\Exception $e){
	            Db::rollback();
	            return ajaxError("操作失败", -1, array());
	        }
			
		}
	}
	
	/**
	 * 
	 * 微信订单支付
	 * 
	 */
	public function pay() {
		if ($this->request->isPost()) {
			$openid = $this->_cookie_openid;
			$order_id = input('post.order_no', '', 'trim');
			//查询订单信息
			$order_header_info = model('MembershipOrderHeader')->where('order_no', $order_id)->find();
			$unifiedOrder = new WechatAppPay(); 
			$unifiedOrder->setParameter("body", "订单".$order_id."支付");              //商品描述
			$unifiedOrder->setParameter("out_trade_no", $order_id);       //商户订单号
//			$unifiedOrder->setParameter("total_fee", $order_header_info['order_amount'] * 100);     //总金额
			$unifiedOrder->setParameter("total_fee", '1'); 
			$unifiedOrder->setParameter("notify_url", config('NOTIFY_URL'));       //通知地址
			$unifiedOrder->setParameter("trade_type", "JSAPI");             //交易类型
			$unifiedOrder->setParameter("openid","$openid");        //用户标识	

			$prepay_id = $unifiedOrder->getPrepayId('public');
			
			$result = array();
			$timeStamp = time();
			$result['appId'] = WechatAppPay::WX_APP_ID;
			$result['timeStamp'] = "$timeStamp";
			$result['nonceStr'] = $unifiedOrder->createNoncestr();
			$result['package'] = "prepay_id=$prepay_id";
			$result['signType'] = "MD5";
			$result['paySign'] = $unifiedOrder->getSign($result);
			
			return json(array('code'=>1, 'payInfo'=>$result));				
			
		}		
	}
	
}