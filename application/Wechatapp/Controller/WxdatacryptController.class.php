<?php
namespace Wechatapp\Controller;

use Common\Controller\HomebaseController;

class WxdatacryptController extends WechatapiController
{
    private $appid;
	private $sessionKey;
	private $wxproappsecret;
	private $OK = 0;
	private $IllegalAesKey = -41001;
	private $IllegalIv = -41002;
	private $IllegalBuffer = -41003;
	private $DecodeBase64Error = -41004;

	/**
	 * 构造函数
	 * @param $sessionKey string 用户在小程序登录后获取的会话密钥
	 * @param $appid string 小程序的appid
	 */
	public function _initialize()
	{
		$this->wxproappsecret = '2b89cb7992d145d6aa913a766aa8568e';
		$this->appid = 'wx2944f7bd9e0cce58';
	}


	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
     *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function decryptData( $sessionKey,$encryptedData, $iv)
	{

		if (strlen($sessionKey) != 24) {
			return $this->IllegalAesKey;
		}
		$aesKey=base64_decode($sessionKey);

        
		if (strlen($iv) != 24) {
			return $this->IllegalIv;
		}
		$aesIV=base64_decode($iv);

		$aesCipher=base64_decode($encryptedData);

		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

		$dataObj=json_decode( $result );
		if( $dataObj  == NULL )
		{
			return $this->IllegalBuffer;
		}
		if( $dataObj->watermark->appid != $this->appid )
		{
			return $this->IllegalBuffer;
		}
		// $data = $result;
		return $result;
		// return $this->OK;
	}
	/**
	 * 跟据小程序传递的code换取用户信息
	 * @return [type] [description]
	 */
	public function wechatuserinfo()
	{
		if (isset($_POST['code'])) {
			$code = $_POST['code'];
			$url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->wxproappsecret.'&js_code='.$code.'&grant_type=authorization_code';
			$res = $this->http_get($url);
			$res = json_decode($res,true);
			// dump($res);exit;
			if (isset($res['session_key']) && isset($res['openid'])) {
				if (isset($_POST['encryptedData']) && isset($_POST['iv'])) {
					$sessionKey = $res['session_key'];
					$encryptedData = $_POST['encryptedData'];
					//需要生成3rd_session
					$iv = $_POST['iv'];
					$info = $this->decryptData($sessionKey,$encryptedData,$iv);
					$info = json_decode($info,true);
					$this->wechatUserinfoPost($info,555,0);
					$session_3rd = $info['openId'];
					$info['sessionKey'] = $sessionKey;
					$data = [
						'code' => 0,
						'status' => "用户信息获取成功",
						'session_3rd' => $session_3rd,
						'info' => $info,
					];
					// $sessionopenid = $sessionKey . "," . $session_3rd;
					
				}else{
					$data = [
						'code' => 1,
						'status' => "用户信息获取失败"
					];
				}
			}
		}else{
			$data = [
				'code' => 2,
				'status' => "session_key获取失败"
			];
			// dump('获取信息失败');exit;
		}
		$this->ajaxReturn($data);
	}
	/**
	 * 生成登录token
	 * @return [type] [description]
	 */
	// public function settoken($openid)
 //    {
 //        $str = md5(uniqid(md5(microtime(true)),true));  //生成一个不会重复的字符串
 //        $openid = strtolower($openid);
        
 //        $str = sha1($str);  //加密
 //        dump($str);exit;
 //        //596368f1d075c87d1960041e88073c3e744faa53
 //        return $str;
 //    }
	public function test()
	{
		$dddd = session('ddddd');
		dump($dddd);exit;
	}
}


