<?php
namespace portal\Controller;
use Common\Controller\HomebaseController; 

class WechatController extends HomebaseController {

    const WXAPPID = 'wx275c7b7becf5e41a';

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

	protected $api_host; 
	public $appId = 'wxc179ff8c019bd102';
    public $appsecret = '0a436c1697b01665a968ca0806f1209a';
    public $request_url;
    public $access_token;
    public $code;
	
	public function _initialize() {
		parent::_initialize();
		$requrl = 'http://wxadmin.efunong.com/index.php?g=portal&m=landpush&a=offerManage';
        $this->request_url = urlEncode($requrl);
	}	
	
	public function getopenid()
	{
		if (empty(cookie('openid'))) {
			$info = $this->get_token();
			$openid = $info['openid'];
		}else{
			// $wxinfo = cookie('wxinfo');
			$openid = cookie('openid');
		}
		return $openid;
	}

    /**
     *  1.配置请求地址，用户同意授权，获取code
     * @return [type] [description]
     */
    public function get_code()  
    {
        //1.配置请求地址，用户同意授权，获取code
        $url = urlencode($url);//加密URL地址
       if (isset($_GET["code"])) {  
            return $_GET["code"];  
        } else {  
            $str = "location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appId . "&redirect_uri=" . $this->request_url . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";  
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
        cookie('openid',$access_token_array['openid'],3600);
        return $access_token_array;  
    }
    /**
     * 获取基本的access_token不同于
     * @return [type] [description]
     */
    public function get_basictoken() 
    { 
    	if (empty(cookie('access_token'))) {
    		$access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appsecret;  
	        $access_token_json = $this->http_get($access_token_url);  
	        $access_token_array = json_decode($access_token_json, TRUE); 
        	cookie('access_token',$access_token_array['access_token'],3600);
        	$access_token = $access_token_array['access_token'];
    	}else{
    		$access_token = cookie('access_token');
    	}
        return $access_token;  
    }
    /**
     * 获取标签
     * @return [type] [description]
     */
    public function get_tags($tagid)
    {
    	$accesstoken = $this->get_basictoken();
    	$tagsurl = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".$accesstoken;  
        $access_token_json = $this->http_get($tagsurl);  
        $access_token_array = json_decode($access_token_json, TRUE);
        //获取标签下的信息
        $tagsinfourl = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=".$accesstoken;
        $params = [
        	"tagid" => $tagid,
        ];
        $tagsinfo =  $this->http_post($tagsinfourl,$params);
        $tagsinfo = json_decode($tagsinfo, TRUE);
        return $tagsinfo;  
    }
    // public function get_taginfos()
    // {
    // 	https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN
    // }
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

    // 判断接口是否请求成功
    public function apiIsOk($code) {
        $res = json_decode($code,true);
        if ($res['code']==0) {
            return $res['data'];
        }else{
            return false;
        }
    }

    // dologin
    public function dologin($data){
        $sign = $this->funongSing($data,self::APPSECEET);
        $urldologin = self::APIHOST.'home/account/api?act=weChatOfficialLogin&appid='.self::APPID.'&sign='.$sign;
        $res = $this->http_post($urldologin,json_encode($data));
        return json_decode($res,true);
        
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

    // 账户初始化信息
    public function initInfo() {
        $data = array('time'=>time());
        $sign = $this->funongSing($data,self::APPSECEET);
        $urlinfo = $this->api_host.'home/account/api?act=initInfo&appid='.self::APPID.'&sign='.$sign;
        $header = array('token:'.cookie('fntoken'),'system:wechat');
        $userinfo = $this->http_post_head($urlinfo,json_encode($data),$header);
        $res_userinfo = json_decode($userinfo,true);
        // var_dump(cookie('fntoken'));
        // var_dump($userinfo);
        // return $res_userinfo['data']['init_list']['account_type'];
        return $res_userinfo;
    }

    // 检索此人是否注册身份，是否是分享客
    public function isfxk($openid) {
        $res = M('fnsmtuser')->where(array('openid'=>$openid))->find();
        if ($res['typeid']==3 || $res['typeid']==4 || $res['typeid']==40 || $res['typeid']==42) {
            return $res['id'];
        }else{
            return false;
        }
    }

    // 生成带参数的二维码
    public function getPubCode($fxkid) {
        $weObj = $this->init(2);
        // 创建ticket
        $ticket = $weObj->getQRCode($fxkid);
        $imgurl = $weObj->getQRUrl($ticket['ticket']);
        return $imgurl;
    }

    // 用户操作行为
    public function userBehavior($data) {
        vendor('logutils');
        $wxappid = 'wx:'.self::WXAPPID;
        $log = new \LogUtils();
        $json_data = json_encode($data);
        // $ip = $_SERVER['REMOTE_ADDR'];
        $ip = $this->getIp();
        // 访问的控制器，方法
        $ma = $_SERVER['QUERY_STRING'];
        $headerinfo = 'IP:'.$ip.'|act:'.$ma.'|data:'.$json_data.'|openid:'.$data['openid'];
        $filename = 'data/log/'.date('Y-m-d').'.txt';
        file_put_contents($filename,$headerinfo,FILE_APPEND);
        // $log->logInfo($headerinfo,$wxappid);
        // return $headerinfo;
    }

    public   function getIp(){
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
}
