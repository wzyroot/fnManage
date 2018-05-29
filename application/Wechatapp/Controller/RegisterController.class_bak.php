<?php
namespace Wechatapp\Controller;

use Common\Controller\HomebaseController;

class RegisterController extends WechatapiController {

	// 报价列表
	protected $api_host;
	protected $wxuser_table;
	protected $fnsmtuser_table;
	protected $fnsmtorder_table;
	protected $userinfos;
	protected $wxproappid;
	protected $wxproappsecret;

	public function _initialize() {
		$this->wxuser_table = M('tradewxuser');
		$this->fnsmtuser_table = M('fnsmtuser');
		$this->wxproappsecret = '2b89cb7992d145d6aa913a766aa8568e';
		$this->wxproappid = 'wx2944f7bd9e0cce58';
	}
	public function isregister($unionid)
	{
		$map['unionid'] = $unionid;
		$isres = $this->fnsmtuser_table->where($map)->find();
		if (empty($isres)) {
			//需增加查询接口是否有会员信息，若有同步到本地数据库，若无注册在本地，和接口
			//$this->接口
			return 1;
		}else{
			return 2;
		}
	}
	//是否注册前端ajax返回
	public function ifregister()
	{
		if (isset($_GET['loginsessionkey']) && !empty($_GET['loginsessionkey'])) {
			$code = $_GET('loginsessionkey');
			$map['openid'] = $code;
			$datainfo = $this->wxuser_table->where($map)->find();
			$unionid = $datainfo['unionid'];
			$map['unionid'] = $unionid;
			$isres = $this->fnsmtuser_table->where($map)->find();
			if (empty($isres)) {
				//需增加查询接口是否有会员信息，若有同步到本地数据库，若无注册在本地，和接口
				//$this->接口
				return 1;
			}else{
				return 2;
			}
		}
	}
	/**
	 * 注册买家或者卖家需提交到接口，司机或者分享客在本地
	 * @return [type] [description]
	 */
	public function register() {
		if (isset($_POST['userinfo']) && !empty($_POST['userinfo'])) {
			$wxappuser = $_POST['userinfo'];
			$wxappuser = json_decode($wxappuser,true);
			$code = $wxappuser['loginsessionkey'];
			$map['openid'] = $code;
			$datainfo = $this->wxuser_table->where($map)->find();
			// $this->ajaxReturn($datainfo);
			file_put_contents('11.txt',$datainfo['openid']);
			$appid = $this->wxproappid;
			$openid = $datainfo['openid'];
			$unionid = $datainfo['unionid'];
			$province = $datainfo['province'];
			$city = $datainfo['city'];
			$isregister = $this->isregister($unionid);
			if ($isregister == 1) {
				//没有注册过;
				$tel = $wxappuser['phone'];
				$typeid = $wxappuser['identity'];
				$name = $wxappuser['username'];
				$vcode = $wxappuser['judgecode'];
				// $fxkid = I('fxkid');//分享客  加入分享客时接入	
				$fxkid = 0;
				$datatime = date("Y-m-d h:i:s");
				if ($typeid == 2) {
					$carnum = $wxappuser['carcode'];
					$carload = $wxappuser['carload'];
					$userinfo = [
						'name' => $name,
						'tel' => $tel,
						'carnum' => $carnum,
						'carload' => $carload,
					];
				}else{
					$userinfo = [
						'name' => $name,
						'tel' => $tel,
					];
				}
				$struser = json_encode($userinfo);
				$data = [
					'appid' => $appid,
					'openid' => $openid,
					'unionid' => $unionid,
					'phone' => $tel,
					'userinfo' => $struser,
					'typeid' => $typeid,
					'source' => $fxkid,//加入分享客时接入	
					'updatatime' => $datatime,	
					'creatime' => $datatime	
				];
				if ($typeid == 0 || $typeid == 1) {
					$apidata = [
						'phone' => $tel,
						'verify_code' => $vcode,
						'public_openid' => $openid,
						'unionid' => $unionid,
						'public_appid' => $appid,
						'name' => $name,
						'identity' => '0',
						'identity_type' => '0',
						'province' => $province,
						'city' => $city,
						'county' => '中国',
						'address' => '中国'
					];
					$register_res = $this->doregister($apidata);
					// file_put_contents('111.txt',json_encode($register_res));
					if (!empty($register_res) && $register_res['code']==0) {
						$map['openid'] = $openid;			
						$wxdata = array('name'=>$name,'tel'=>$tel);
						$this->wxuser_table->where($map)->setField($wxdata);			
						$this->fnsmtuser_table->add($data);

						$token = $register_res['data']['token'];
						// file_put_contents('222.txt',$register_res);
						cookie('fntoken',$token,3600);
						
						$this->userBehavior($register_arr);

						// 发送模板消息
						$this->sendTempMsg($register_arr);
						$respost = [
							'code' => 0,
							'res' => $res,
							'status' => '注册成功',
						];
					}else{
						$respost = [
							'code' => 4001,
							'res' => $res,
							'status' => '注册失败',
						];
					}
				}else{
					
					//判断场景值
					// $wxuser_res = $this->wxuser_table->where($map)->find();
		    		// if ($wxuser_res['sceneid']!=0) {
		    		// 	$data['source'] = $wxuser_res['sceneid'];
		    		// }
		    		$vcode = $wxappuser['vcode'];
					$judgecode = $wxappuser['judgecode'];
					if ($vcode != $judgecode) {
						$respost = [
							'code' => -222222,
							'res' => $res,
							'status' => '请输入正确的验证码',
						];
					}else{
						$res = $this->fnsmtuser_table->add($data);
						$respost = [
							'code' => 0,
							'res' => $res,
							'status' => '注册成功',
						];
					}
				}
				// $this->userBehavior($data);//写入日志
			}else{
				$respost = [
					'code' => 2001,
					'status' => '已经注册过',
				];
			}
		}else{
			$respost = [
				'code' => 3001,
				'status' => '登陆状态异常',
			];
		}
		$this->ajaxReturn($respost);
	}


	/**
	 * 获取登录信息传值内容分为:
	 * $dologindata = array('public_openid'=>$openid,'public_appid'=>$appid,'unionid'=>$user_res['unionid']);
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function dologin($data){
		$sign = $this->funongSing($data,self::APPSECEET);
		$urldologin = self::APIHOST.'home/account/api?act=weChatOfficialLogin&appid='.self::APPID.'&sign='.$sign;
		$res = $this->http_post($urldologin,json_encode($data));
		return json_decode($res,true);
	}
	/**
	 * 账户初始化信息:从接口拉取用户信息
	 * 当登陆状态token存在，并且本地数据库中没有信息记录时查询
	 * @return [type] [description]
	 */
	public function initInfo() {
		$data = array('time'=>time());
		$sign = $this->funongSing($data,self::APPSECEET);
		$urlinfo = $this->api_host.'home/account/api?act=initInfo&appid='.self::APPID.'&sign='.$sign;
		$header = array('token:'.cookie('fntoken'),'system:wechat');
		$userinfo = $this->http_post_head($urlinfo,json_encode($data),$header);
		$res_userinfo = json_decode($userinfo,true);
		return $res_userinfo;
	}
	/**
	 * 注册信息向接口提交
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function doregister($data){
		$sign = $this->funongSing($data,self::APPSECEET);
		$doregister = $this->api_host.'home/account/api?act=weChatOfficialRegister&appid='.self::APPID.'&sign='.$sign;
		$res = $this->http_post($doregister,json_encode($data));
		return json_decode($res,true);
	}

	// 发送验证码
	public function sendVerifyCode(){
		$tel = I('tel');
		$data = array('verify_type'=>'REGISTER','phone'=>$tel);
		$sign = $this->funongSing($data,self::APPSECEET);
		$vc = $this->api_host.'home/account/api?act=sendVerifyCode&appid='.self::APPID.'&sign='.$sign;
		
		$res = $this->http_post($vc,json_encode($data));
		$this->ajaxReturn(json_decode($res,true));
	}
	public function test()
	{
		dump(123456);
	}
}
