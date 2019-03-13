<?php
namespace app\api\controller;

use think\Db;

use app\common\WechatAppPay;

use think\Controller;

class Notify extends Controller {
	
	protected $order_id = '0';
    public function _initialize()
    {
        $notify = new WechatAppPay();

        //存储微信的回调
//        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];  php7不支持此函数，微信建议用 file_get_contents("php://input")
        $xml = file_get_contents("php://input");

        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){

            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{

            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
            $notify->setReturnParameter("return_msg","OK");
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
        
        $return_code = $notify->data["return_code"];
        $order_id = $notify->data["out_trade_no"];
        
		Db::execute("insert into yuet_merchant_test(`name`) values('返回码：".$return_code."；订单编号：".$order_id."；时间：".date('Y-m-d H:i:s')."')");

        $this->order_id = $order_id;
    }
	
    /**
     * 
     * 回调地址
     * 
     */
	public function index() {
		
		$order_id = $this->order_id;
        $order = model('MembershipOrderHeader')->alias('a')
        	   ->join('yuet_merchant.yuet_merchant_membership_order_log b','a.order_no=b.member_order_no','left')
        	   ->join('yuet_merchant.yuet_merchant_membership_order_detail d','a.order_no=d.order_no','left')
        	   ->join('yuet_merchant.yuet_merchant_user c', 'a.user_id=c.id', 'left')
        	   ->field('a.*,b.*,c.*,d.*,a.order_amount as a_order_amount,d.order_amount as d_order_amount,a.merchant_id as a_merchant_id,a.user_name as a_user_name')
        	   ->where('a.order_no', $order_id)->find();
        $order = empty($order) ? array():$order->toArray();	   

        $membership = model('MembershipCard')->where('code', $order['membership_code'])->find();	   
        $membership = empty($membership) ? array():$membership->toArray();	   

        if(empty($order))
        {
            exit();
        }
        if($order['order_status'] != 0)
        {
            exit();
        }          

        Db::startTrans();
        try{
        	
        	//1.修改订单主表的订单状态,支付类型,支付时间
        	$option = array();
        	$option['order_status'] = 10;
        	$option['payment_type'] = 1;
        	$option['pay_time'] = date('Y-m-d H:i:s',time());        	
        	model('MembershipOrderHeader')->where('order_no', $order_id)->update($option);
        	
        	//2.修改订单日志表的支付金额,支付类型,支付时间,总金额,操作前额度,当前额度,剩余余额
        	$user_membership_condition['user_id'] = $order['user_id'];
        	$user_membership_condition['membershipcard_sub_code'] = $order['membership_sub_code'];
        	$user_membership_condition['status'] = 0;
        	$user_membership = model('UserMembershipInfo')->getCondition($user_membership_condition); 
        	
        	$total_amount = isset($user_membership['total_amount']) ? $user_membership['total_amount']: 0;
        	$balance = isset($user_membership['balance']) ? $user_membership['balance']: 0;
        	$expired_time = isset($user_membership['expired_time']) ? $user_membership['expired_time']: 0;
        	        	
        	$option = array();
        	$option['total_amount'] = $order['total_amount'] + $total_amount; 
        	$option['before_amount'] = $balance ? $balance : 0;
        	$option['current_amount'] = $order['a_order_amount'];
        	$option['after_amount'] = $balance + $order['a_order_amount'];
        	$option['payment_amount'] = $order['a_order_amount'];
        	$option['payment_type'] = 1;
        	$option['payment_time'] = date('Y-m-d H:i:s',time()); 
        	model('MembershipOrderLog')->where('member_order_no', $order_id)->update($option);

        	//3.修改用户表的类型,是否为首次
        	$option = array();
        	if ($order['type'] == 0) {
        		$option['type'] = 1;
        		$option['is_first_buy'] = 1;
        		$option['user_name'] = $order['a_user_name'];
        		if ($order['pay_sex'] != '') {
        			$option['sex'] = $order['pay_sex'];
        		}
        		model('User')->where('id', $order['user_id'])->update($option);
        	} 

        	//4.用户会员表添加数据
        	$option = array();
        	if ($user_membership) {
				//如果当前时间小于过期时间,则修改余额,总金额,有过期时间
				if (time() < $expired_time) {
					$member_ship_option = array();
					$member_ship_option['expired_time'] = strtotime("+1 year") + $user_membership['expired_time'];
					$member_ship_option['total_amount'] = $order['d_order_amount'] + $order['fee_amount'] + $total_amount;
					$member_ship_option['balance'] = $order['d_order_amount'] + $order['fee_amount'] + $balance;
					model('UserMembershipInfo')->where($user_membership_condition)->update($member_ship_option);
				//如果当前时间大于过期时间,则重新创建一条数据
				} else {
					$option['user_id'] = $order['user_id'];
					$option['court_id'] = $membership['court_id'];
					$option['merchant_id'] = $order['a_merchant_id'];
					$option['membershipcard_id'] = $membership['id'];
					$option['membershipcard_sub_code'] = $order['membership_sub_code'];
					$option['total_amount'] = $order['d_order_amount'] + $order['fee_amount'];
					$option['balance'] = $order['d_order_amount'] + $order['fee_amount'];
					$option['create_time'] = date('Y-m-d H:i:s',time());
					$option['expired_time'] = strtotime("+1 year");					
				}
			//如果在用户会员卡中不存在							
			} else {
				$option['user_id'] = $order['user_id'];
				$option['court_id'] = $membership['court_id'];
				$option['merchant_id'] = $order['a_merchant_id'];
				$option['membershipcard_id'] = $membership['id'];
				$option['membershipcard_sub_code'] = $order['membership_sub_code'];
				$option['total_amount'] = $order['d_order_amount'] + $order['fee_amount'];
				$option['balance'] = $order['d_order_amount'] + $order['fee_amount'];
				$option['create_time'] = date('Y-m-d H:i:s',time());
				$option['expired_time'] = strtotime("+1 year");
			}       	
        	model('UserMembershipInfo')->insert($option);
        	
        	//5.商户与平台按照比例进行拆单
        	$option = array();
        	$merchant = model('Merchant')->where('id', $order['a_merchant_id'])->find()->toArray();
        	foreach (explode(":", $merchant['apportion_percent']) as $k=>$v) {
				$option[$k]['order_no'] = $order['order_no'];
				$option[$k]['split_percent'] = $merchant['apportion_percent'];
				if ($k == 0) {
					$option[$k]['split_order_no'] = 'MC_'.$order['order_no'];
					$option[$k]['order_amount'] = $order['a_order_amount'] * ($v/100);
				} else {
					$option[$k]['split_order_no'] = 'PF_'.$order['order_no'];
					$option[$k]['order_amount'] = $order['a_order_amount'] * ($v/100);
				}
				$option[$k]['create_time'] = date('Y-m-d H:i:s',time());
			}        	
        	model('MembershipSplitOrder')->insertAll($option);

        	//6.会员卡字段 会员卡总人数加1
        	if (!$membership) {
        		model('MembershipCard')->where('id', $membership['id'])->setInc('member_count');
        	}
            Db::commit();
            exit("succ");
        }catch (\Exception $e){
            Db::rollback();
            exit("error");
        }
	}
}