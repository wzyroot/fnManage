<?php
namespace Landpush\Controller;
use Common\Controller\HomebaseController;
class OffermanageController extends WechatController {

	protected $api_host;
	protected $groups;
	protected $landusers;
	protected $pushers;
	
	public function _initialize() {
		parent::_initialize();
	}	
	public function offerManage()
	{
		if (!empty($_GET['code']) || !empty($_GET['openid'])) {
			if (!empty($_GET['openid'])) {
				$openid = $openidres['openid'];
			}else{
				$code = $_GET['code'];
				$openidres = $this->htmlopenid($code);
				$openid = $openidres['openid'];
			}
			$total = [
				"oPJay0w2TBokk1lU1REEN-dkYmFo",
				"oPJay08CPgUf9aP9mSVD16uEQkYs",
				"oPJay08IGitfoqIGnfM2QLpIL7rw",
				"oPJay0x6NltflPYQs2riXqRdoiwM",
			];
			if (in_array($openid,$total)) {
				// $offerurl = 'http://192.168.0.119:3333/trade/offer/api?act=modifyGoodsOfferBySystem';
				$offerurl = 'http://121.43.50.178:89/trade/offer/api?act=modifyGoodsOfferBySystem';
				$data = [
					'action_type'=>'SOLD_OUT',
					'is_select_all'=>'1',
				];
				$info = $this->http_post($offerurl,json_encode($data));
				$info = json_decode($info,true);
				$msg = $info['message'];
				$data['message'] = $msg;
			}else{
				$msg = '暂未开放';
				$data['message'] = $msg;
			}
			$data['openid'] = $openid;
		}else{
			$msg = '暂未开放';
			$data['message'] = $msg;
		}
		
		$this->ajaxReturn($data);
		// $this->assign('msg',$msg);
		// $this->display();
	}
	public function test()
	{
		if (!empty($_GET['code'])) {
			$code = $_GET['code'];
			$openidres = $this->htmlopenid($code);
			$openid = $openidres['openid'];
		}else{
			$openid = '没有';
		}
		$this->ajaxReturn(array('openid'=>$openid));
		// $this->ajaxReturn($openid);
		// return 1;
	}
}
