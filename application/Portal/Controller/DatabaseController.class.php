<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 

class DatabaseController extends WechatController {

	protected $api_host;
	
	public function _initialize() {
		parent::_initialize();
// 		$this->wechat_initialize();
		$this->fnsmtorder_table = M('fnsmtorder');
		$this->tradewxuser_table = M('tradewxuser');
		$this->fnsmtuser_table = M('fnsmtuser');

		// $this->api_host = 'http://192.168.0.119:2222/';
		// $this->api_host = 'http://inner.efunong.com:89/';
		$this->api_host = 'http://121.43.50.178:81/';
	}
	
	public function minmarket()
	{
		$this->display();
	}

	public function purchase()
	{
		echo '调试中......';
		// $this->display();
	}
	public function purchaseopen()
	{
		$this->display();
	}
	public function sale()
	{
		// echo '调试中......';
		$this->display();
	}
	public function userdata()
	{
		$this->display();
	}
	public function index()
	{
		$this->display();
	}
	public function saleopen()
	{
		$this->display();
	}
	/**
	 * 用户统计
	 * @return [type] [description]
	 */
	public function datainfo()
	{
		if (empty(I('days'))) {
			$days = 7;
		}else{
			$days = I('days');
		}
		if (empty(I('end_time'))) {
			$end_time = time();
		}else{
			$end_time = I('end_time');
		}
		$data = [
			'days' => $days,
			'end_time' => $end_time
		];
		$sign = $this->funongSing($data,$this->appsecret);
		$urlnews_list = $this->api_host."dealers/contract/api?act=countAccount&appid=".$this->appid."&sign=".$sign;
		$articles = $this->apiIsOk($this->http_post($urlnews_list,json_encode($data)));
		// dump($articles);exit;
		$this->ajaxReturn($articles);
	}
	/**
	 * 采/销平衡
	 * @return [type] [description]
	 */
	public function salepurchase()
	{
		if (empty(I('days'))) {
			$days = 1;
		}else{
			$days = I('days');
		}
		if (empty(I('end_time'))) {
			$end_time = time();
		}else{
			$end_time = I('end_time');
		}
		$data = [
			'days' => $days,
			'end_time' => $end_time
		];
		$sign = $this->funongSing($data,$this->appsecret);
		$urlnews_list = $this->api_host."dealers/contract/api?act=countContractNum&appid=".$this->appid."&sign=".$sign;
		$datainfo = $this->apiIsOk($this->http_post($urlnews_list,json_encode($data)));
		// dump($datainfo);exit;
		foreach ($datainfo['purchase'] as $key => $value) {
			$datainfo['purchase'][$key]['purchaseamount_num'] = $value['amount_num'];
			$datainfo['purchase'][$key]['purchaseamount_money'] = $value['amount_money'];
			unset($datainfo['purchase'][$key]['amount_num']);
			unset($datainfo['purchase'][$key]['amount_money']);
		}
		foreach ($datainfo['sale'] as $k => $v) {
			$datainfo['sale'][$k]['saleamount_num'] = $v['amount_num'];
			$datainfo['sale'][$k]['saleamount_money'] = $v['amount_money'];
			unset($datainfo['sale'][$k]['amount_num']);
			unset($datainfo['sale'][$k]['amount_money']);
		}
		$data = array_merge($datainfo['purchase'],$datainfo['sale']);
		$info = [];
		foreach ($data as $x => $y) {
			$info[$y['good_id']][] = $y;
		}
		$temp = [];
		foreach ($info as $a => $b) {
			if (count($b) == 2) {
				$temp['data']['offer_list'][] = array_merge($b[0],$b[1]);
			}else{
				foreach ($b as $key => $value) {
					$temp['data']['offer_list'][] = $value;
				}
			}
		}
		$temp['data']['purchase_count'] = $datainfo['purchase_count'];
		$temp['data']['sale_count'] = $datainfo['sale_count'];
		// dump($temp);exit;
		$this->ajaxReturn($temp);
		
	}
	/**
	 * 销售合同开单统计
	 * @return [type] [description]
	 */
	public function saleopenorder()
	{
		if (empty(I('days'))) {
			$days = 7;
		}else{
			$days = I('days');
		}
		if (empty(I('end_time'))) {
			$end_time = time();
		}else{
			$end_time = I('end_time');
		}
		if (empty(I('kind')) || I('kind') == '全部品种') {
			$data = [
				'days' => $days,
				'end_time' => $end_time
			];
		}else{
			$kind = I('kind');
			$data = [
				'days' => $days,
				'end_time' => $end_time,
				'kind' => $kind
			];
		}
		// dump($data);
		$sign = $this->funongSing($data,$this->appsecret);
		$urlnews_list = $this->api_host."/dealers/contract/api?act=countSellOrder&appid=".$this->appid."&sign=".$sign;
		// $urlnews_list = "http://192.168.0.119:2222/dealers/contract/api?act=countSellOrder&appid=".$this->appid."&sign=".$sign;
		$articles = $this->apiIsOk($this->http_post($urlnews_list,json_encode($data)));
		if (isset($articles['kinds'])) {
			array_unshift($articles['kinds'],'全部品种');
		}
		// dump($articles);exit;
		$this->ajaxReturn($articles);
	}
	/**
	 * 采购开单统计
	 * @return [type] [description]
	 */
	public function purchaseopenorder()
	{
		if (empty(I('days'))) {
			$days = 7;
		}else{
			$days = I('days');
		}
		if (empty(I('end_time'))) {
			$end_time = time();
		}else{
			$end_time = I('end_time');
		}
		if (empty(I('kind')) || I('kind') == '全部品种') {
			$data = [
				'days' => $days,
				'end_time' => $end_time
			];
		}else{
			$kind = I('kind');
			$data = [
				'days' => $days,
				'end_time' => $end_time,
				'kind' => $kind
			];
		}
		// dump($data);
		$sign = $this->funongSing($data,$this->appsecret);
		$urlnews_list = $this->api_host."dealers/contract/api?act=countPurchaseOrder&appid=".$this->appid."&sign=".$sign;
		// $urlnews_list = "http://192.168.0.119:2222/dealers/contract/api?act=countPurchaseOrder&appid=".$this->appid."&sign=".$sign;
		$articles = $this->apiIsOk($this->http_post($urlnews_list,json_encode($data)));
		if (isset($articles['kinds'])) {
			array_unshift($articles['kinds'],'全部品种');
		}
		// dump($articles);exit;
		$this->ajaxReturn($articles);
	}
	/**
	 * 销售统计
	 * @return [type] [description]
	 */
	public function saleinfo()
	{
		if (empty(I('days'))) {
			$days = 7;
		}else{
			$days = I('days');
		}
		if (empty(I('end_time'))) {
			$end_time = time();
		}else{
			$end_time = I('end_time');
		}
		$data = [
			'days' => $days,
			// 'end_time' => $end_time
		];
		$sign = $this->funongSing($data,$this->appsecret);
		$urlnews_list = $this->api_host."dealers/contract/api?act=countDeposit&appid=".$this->appid."&sign=".$sign;
		// $urlnews_list = "http://192.168.0.119:2222/dealers/contract/api?act=countDeposit&appid=".$this->appid."&sign=".$sign;
		$articles = $this->apiIsOk($this->http_post($urlnews_list,json_encode($data)));
		// dump($articles);exit;
		$this->ajaxReturn($articles);
	}
}
