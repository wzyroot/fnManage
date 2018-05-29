<?php
namespace Wechatapp\Controller;

class IndexController extends WechatapiController {

	// 报价列表
	public function offerlist(){
		$ip = $this->getIp();
		$data = array(
			'ip'=>$ip,
			'action_type'=>1,
			'page'=>1
		);
		$res = $this->getData('home/offer',$data,'recommendGoodsOffer','0');
		dump($res);
	}
}
