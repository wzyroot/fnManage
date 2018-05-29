<?php
namespace Landpush\Controller;
use Common\Controller\HomebaseController; 

class WechatController extends HomebaseController {

	protected $api_host; 
    // public $appId = 'wx3c7d00dc5aeb19e8';
    // public $appsecret = 'e38d839bbf94366c6096f73d40b79d29';
	// public $appId = 'wx9f29cc55ada26bfa';
 //    public $appsecret = 'ebf33c885a13253345c0d9b96b6704bc';
    public $appId = 'wxc179ff8c019bd102';
    public $appsecret = '0a436c1697b01665a968ca0806f1209a';
    public $request_url;
    public $access_token;
    public $code;
	
	public function _initialize() {
		parent::_initialize();
        header('Access-Control-Allow-Origin: *');
        //$requrl = 'http://tms.inner.efunong.com/index.php?g=landpush&m=landpush&a=index';
		//$requrl = 'http://tms.inner.efunong.com/index.php?g=landpush&m=landpush&a=authorityJudge';
        //$this->request_url = urlEncode($requrl);
	}	
	
	public function getopenid()
	{
		// if (empty(cookie('openid'))) {
			$info = $this->get_token();
			$openid = $info['openid'];
		// }else{
		// 	// $wxinfo = cookie('wxinfo');
		// 	$openid = cookie('openid');
		// }
		return $openid;
	}

    /**
     *  1.配置请求地址，用户同意授权，获取code
     * @return [type] [description]
     */
    public function get_code()  
    {
        //1.配置请求地址，用户同意授权，获取code
        // $url = urlencode($url);//加密URL地址
       if (isset($_GET["code"])) {  
            return $_GET["code"];  
        } else {  
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $requesturi = urlEncode($url);
            $str = "Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appId . "&redirect_uri=" . $requesturi . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";  
            header($str);  
            exit;  
        }  
    }
    /**
     * 2.通过code换取网页授权access_token
     * @return [type] [description]
     */
    public function get_token() 
    {
    	$code = $this->get_code(); 
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appId . "&secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code";  
        $access_token_json = $this->http_get($access_token_url);  
        $access_token_array = json_decode($access_token_json, TRUE); 
        // cookie('openid',$access_token_array['openid'],3600);
        return $access_token_array;  
    }

    
    /**
     * 获取基本的access_token不同于
     * @return [type] [description]
     */
    // public function get_basictoken() 
    // { 
    // 	if (empty(S('accesstoken'))) {
    //         $access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appsecret;  
    //         $access_token_json = $this->http_get($access_token_url);  
    //         $access_token_array = json_decode($access_token_json, TRUE); 
    //         $access_token = $access_token_array['access_token'];
    //         S('accesstoken',$access_token,7000);
    //     }else{
    //         $access_token = S('accesstoken');
    //     }
    //     return $access_token;  
    // }

    private function getAccessToken() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("access_token.json"));
        if ($data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appsecret;
            $res = json_decode($this->http_get($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                file_put_contents("access_token.json", json_encode($data));
                // $fp = fopen("access_token.json", "w");
                // fwrite($fp, json_encode($data));
                // fclose($fp);
            }
        } else {
          $access_token = $data->access_token;
        }
        return $access_token;
    }
    /**
     * 获取标签
     * @return [type] [description]
     */
    public function get_tags($tagid)
    {
    	$accesstoken = $this->getAccessToken();
        // $accesstoken = $this->getAccessToken();
    	$tagsurl = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$accesstoken;  
        $access_token_json = $this->http_get($tagsurl);  
        $access_token_array = json_decode($access_token_json, TRUE);
        //获取标签下的信息
        $tagsinfourl = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=".$accesstoken;
        $params = [
        	"tagid" => $tagid,
        ];
        $tagsinfo =  $this->http_post($tagsinfourl,json_encode($params));
        $tagsinfo = json_decode($tagsinfo, TRUE);
        return $tagsinfo;  
    }
    //所有标签
    public function testget_tags()
    {
        $accesstoken = $this->getAccessToken();
        $tagsurl = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$accesstoken;  
        $access_token_json = $this->http_get($tagsurl);  
        $access_token_array = json_decode($access_token_json, TRUE);
        return $access_token_array;  
    }
    public function selectTags($openid)
    {
        $accesstoken = $this->getAccessToken();
        $tagsurl = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=".$accesstoken;
        $data = [
            "openid" => $openid,
        ];  
        $access_token_json = $this->http_post($tagsurl,json_encode($data));
        $access_token_array = json_decode($access_token_json, TRUE);
        return $access_token_array;  
    }

    /**
     * 前端获取openid
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function htmlopenid($code) 
    {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appId . "&secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code";  
        $access_token_json = $this->http_get($access_token_url);  
        $access_token_array = json_decode($access_token_json, TRUE); 
        cookie('openid',$access_token_array['openid'],3600);
        return $access_token_array;  
    }
    /**
     * 前端获取access_token
     * @return [type] [description]
     */
    public function htmltoken() 
    { 
        if (empty(S('accesstoken'))) {
            $access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appsecret;  
            $access_token_json = $this->http_get($access_token_url);  
            $access_token_array = json_decode($access_token_json, TRUE); 
            $access_token = $access_token_array['access_token'];
            S('accesstoken',$access_token,7200);
        }else{
            $access_token = S('accesstoken');
        }
        
        return $access_token;  
    }

    /**
     * 微信jsd接口验签
     * @return [type] [description]
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
          "appId"     => $this->appId,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        return $signPackage; 
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accessToken;
            $res = json_decode($this->http_get($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }
    // 判断接口是否请求成功
    public function apiIsOk($code) {
        $res = json_decode($code,true);
        if ($res['code']==0) {
            return $res['data'];
        }else{
            return false;
        }
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
}


