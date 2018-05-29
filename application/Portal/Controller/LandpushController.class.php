<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class LandpushController extends WechatController {

	protected $api_host;
	protected $groups;
	protected $landusers;
	protected $pushers;
	
	public function _initialize() {
		parent::_initialize();
	}	
	public function offerManage()
	{
		$total = [
			"oPJay0w2TBokk1lU1REEN-dkYmFo",
			"oPJay08CPgUf9aP9mSVD16uEQkYs",
			"oPJay08IGitfoqIGnfM2QLpIL7rw",
			"oPJay0x6NltflPYQs2riXqRdoiwM",
		];
		$openid = $this->getopenid();
		if (in_array($openid,$total)) {
			$offerurl = 'http://121.43.50.178:82/trade/offer/api?act=modifyGoodsOfferBySystem';
			$data = [
				'action_type'=>'SOLD_OUT',
				'is_select_all'=>'1',
			];
			$info = $this->http_post($offerurl,json_encode($data));
			$info = json_decode($info,true);
			$msg = $info['message'];
		}else{
			$msg = '暂未开放';
		}
		$this->assign('msg',$msg);
		$this->display();
	}
}
