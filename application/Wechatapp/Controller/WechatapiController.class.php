<?php
namespace Wechatapp\Controller;

use Common\Controller\HomebaseController;

class WechatapiController extends HomebaseController {

	//公众号appid
	const WXAPPID = 'wx275c7b7becf5e41a';
	//小程序appid
	const WXPROAPPID = 'wx275c7b7becf5e41a';
	// 吴杨
	const APPID = '51519633655';
	const APPSECEET = 'B5AD18E67CE28B86864F847261F98011';
	// 晓宗
	// const APPID = '51524793738';
	// const APPSECEET = 'B8BF8D2EC7304344136F1A8C6869FF8F';

	// 正式服务器
	// const APIHOST = 'http://121.43.50.178:81/';
	// 晓宗
	// const APIHOST = 'http://192.168.0.10:83/';
	// 吴杨
	// const APIHOST = 'http://192.168.0.119:2222/';
	// 测试服务器
	// const APIHOST = 'http://inner.efunong.com:89/';
	const APIHOST = 'http://192.168.0.240:89/';

	public function getData($urltype,$data,$act,$isheader) {
		$sign = $this->funongSing($data,self::APPSECEET);
		$url = self::APIHOST.$urltype.'/api?act='.$act.'&page='.$data['page'].'&appid='.self::APPID.'&sign='.$sign;
		if ($isheader==0) {
			$res = $this->http_post($url,json_encode($data));
		}else{
			$header = array('token:'.cookie('fntoken'),'system:wechatapp');
			$res = $this->http_post_head($url,json_encode($data),$header);
		}
		$res_arr = json_decode($res,true);
		return $res_arr;
	}

    	// 生成sing
	public function funongSing($array,$appsecret){
		// var_dump($array);exit();
		foreach ($array as $key=>$value){
   			$arr[$key] = $key; 
 		}
 		sort($arr);

		$str = "";
		foreach ($arr as $k => $v) {
			if(is_array($array[$v])) {
     				$str = $str.$arr[$k].json_encode($array[$v]);
			}else{
     				$str = $str.$arr[$k].$array[$v];
			}
		}
		$restr=$str.$appsecret;
		$sign = strtoupper(sha1($restr));
		return $sign;
	}

	public function getIp(){
	 	$ip='未知IP';
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    			return $this->is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    			return $this->is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
		}else{
    			return $this->is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
		}
    }
    
   	public function is_ip($str){
		$ip=explode('.',$str);
		for($i=0;$i<count($ip);$i++){
    			if($ip[$i]>255){
        			return false;
    			}
		}
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);
	}
	/**
	 * 基本信息写入
	 * @param  [type] $resuserinfo [description]
	 * @param  [type] $accountid   [description]
	 * @param  [type] $sceneid     [description]
	 * @return [type]              [description]
	 */
	public function wechatUserinfoPost($resuserinfo,$accountid,$sceneid) {
		$tradewxuser = M('tradewxuser');
		$isnull = $tradewxuser->where(array("openid"=>$resuserinfo['openId']))->find();
		if(empty($isnull)){
			$data ["typeid"] = 0;
			$data ["openid"] = $resuserinfo['openId'];
			$data ["nickname"] = json_encode(array('nickname'=>$resuserinfo['nickName']));
			$data ["sex"] = 5;
			$data ["province"] = $resuserinfo['province'];
			$data ["city"] = $resuserinfo['city'];
			$data ["headimgurl"] = $resuserinfo['avatarUrl'];
			$data ["unionid"] = $resuserinfo['unionId'];
			$data ["updatatime"] = date("Y-m-d H:i:s", time());
			$data ["createtime"] = date("Y-m-d H:i:s", time());
			$data ["isguanzhu"] = 5;
			$data ["sceneid"] = $sceneid;
			$data ["publicid"] = $accountid;
			//print_r($data);exit;
			cookie('openid',$resuserinfo['openId']);
			if (!empty($resuserinfo['nickName'])) {
				$tradewxuser->add($data);
			}
		}else{
			if (!empty($resuserinfo['nickName'])) {
				if ($isnull['isguanzhu']==0) {
					$data ["isguanzhu"] = 1;
				}
				$data ["nickname"] = json_encode(array('nickname'=>$resuserinfo['nickName']));
				$data ["sex"] = 5;
				$data ["province"] = $resuserinfo['province'];
				$data ["city"] = $resuserinfo['city'];
				$data ["headimgurl"] = $resuserinfo['avatarUrl'];
				$data ["updatatime"] = date("Y-m-d H:i:s", time());
				$data ["publicid"] = $accountid;
				$map['openid'] = $isnull['openid'];
				cookie('openid',$resuserinfo['openId']);
				$tradewxuser->where($map)->save($data);
			}
		}
	}
	public function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	public function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	// 设置头信息
	public function http_post_head($url,$param,$header,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
}
