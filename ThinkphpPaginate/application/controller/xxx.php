<?php
use app\admin\controller\Base;
class xxx extends Base {
	/**
	 * 
	 * 详情
	 * 
	 */
	public function showDetail() {
		if ($this->request->isGet()) {
			$id = input('id','0','trim');
			$row = Db::table('yuet_merchant.yuet_merchant_user')
					->where('id',$id)
					->find();
			$result = model('TableInfo')->getTableInfo('yuet_merchant.yuet_merchant_user'); 		
			$row['order_total'] = model('Member')->getOrderCountByUserNick($row['user_nick']);
			$row['refund_total'] = model('Member')->getRefundOrderCountByUserNick($row['user_nick']);
			$row['consume_total'] = model('Member')->getConsumeAmountTotal($row['user_nick']);
			$row['membership_card_total'] = model('Member')->getmembershipCardTotal($row['user_nick']);	
			$row['type'] = $result['type'][$row['type']];	

			$this->assign('row',$row);	
			$this->orderDetail($id, $row['user_nick']);
			$this->membershipcardDetail($id, $row['user_nick']);
			return $this->fetch('showDetail');	
		}
	}
	
	
	/**
	 * 
	 * 订单详情
	 * @param $user_nick 用户昵称
	 * 
	 */
	public function orderDetail($id, $user_nick='') { 
		if ($this->request->isGet()) {
			$where = [];
			if ($user_nick) {
				$where['user_nick'] = $user_nick ? $user_nick : input('user_nick','','trim');
			}

			$count = Db::table('yuet_merchant.yuet_merchant_order_header')
				  ->alias('a')
				  ->join('yuet_merchant.yuet_merchant_order_detail b','a.order_no=b.order_no','left')
				  ->where($where)
				  ->order('a.id desc')
				  ->count();			 
	    				  
			$list = Db::table('yuet_merchant.yuet_merchant_order_header')
				  ->alias('a')
				  ->join('yuet_merchant.yuet_merchant_order_detail b','a.order_no=b.order_no','left')
				  ->where($where)
				  ->order('a.id desc')
				  ->paginate(20,$count,[
						'page' => input('param.page'),
				  		'path'=>'/member/showDetail/id/'.$id.'/page/[PAGE]'.'.html',
					])
				  ->each(function($item, $key){
				  		$result = model('TableInfo')->getTableInfo('yuet_merchant.yuet_merchant_order_header'); 
						$court_info = model('Court')->getCourtInfoById($item['court_id']);
						$merchant_info = model('Merchant')->getMerchantInfoById($item['merchant_id']);
						$item['order_status'] = $result['order_status'][$item['order_status']];
						$item['court_id'] = $court_info['name'];
						$item['merchant_id'] = $merchant_info['merchant_name'];
						$item['order_amount_total'] = model('Member')->getConsumeAmountTotal($item['user_nick']);
						$item['membership_card_amount_total'] = model('Member')->getmembershipCardTotal($item['user_nick']);
					    return $item;
					});

			// 获取分页显示
			$page = $list->render();	
			$orderDetail = array('list'=>$list, 'page'=>$page);
			$this->assign('orderdetail',$orderDetail);
			$this->fetch('orderDetail');				
		}
	}
	
	
	/**
	 * 
	 * 会员卡详情
	 * 
	 */
	public function membershipcardDetail($id, $user_nick='') {
		if ($this->request->isGet()) {
			$where = [];
			if ($user_nick) {
				$where['a.user_nick'] = $user_nick ? $user_nick : input('user_nick','','trim');
			}

			$count = Db::table('yuet_merchant.yuet_merchant_membership_order_log')
				  ->alias('a')
				  ->join('yuet_merchant.yuet_merchant_membership_card b','a.membership_code=b.code','left')
				  ->where($where)
				  ->order('a.id desc')
				  ->count();			 
	    				  
			$list = Db::table('yuet_merchant.yuet_merchant_membership_order_log')
				  ->alias('a')
				  ->join('yuet_merchant.yuet_merchant_membership_card b','a.membership_code=b.code','left')
				  ->where($where)
				  ->order('a.id desc')
				  ->paginate(20,$count,[
						'page' => input('param.page'),
				  		'path'=>'/member/showDetail/id/'.$id.'/page/[PAGE]'.'.html',
					])
				  ->each(function($item, $key){
				  		$membership_order_log = model('TableInfo')->getTableInfo('yuet_merchant.yuet_merchant_membership_order_log'); 
				  		$membership_card = model('TableInfo')->getTableInfo('yuet_merchant.yuet_merchant_membership_order_log'); 
						$court_info = model('Court')->getCourtInfoById($item['court_id']);
						$merchant_info = model('Merchant')->getMerchantInfoById($item['merchant_id']);
						$item['status'] = $membership_card['order_status'][$item['status']];
						$item['court_id'] = $court_info['name'];
						$item['merchant_id'] = $merchant_info['merchant_name'];
						$item['pay_method'] = $membership_order_log['pay_method'][$item['pay_method']];
					    return $item;
					});

			// 获取分页显示
			$page = $list->render();	
			$membershipcardDetail = array('list'=>$list, 'page'=>$page);
			$this->assign('membershipcarddetail',$membershipcardDetail);
			$this->fetch('membershipcardDetail');				
		}	
	}	
}