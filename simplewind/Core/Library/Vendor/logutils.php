<?php
#调用方式
/*
require_once dirname(__file__) . '/utils/LogUtils.php';
LogUtils::logInfo($info,$tag);
*/
/*
注意：依次解决wx 、api、erp、tmsManager，对于app由其它的机制实现。

$tag是日志标签，格式如下：
#app:【版本号】    //商贸通APP,如：app:4.0.0
wx:【appId】      //微信网页,如：wx:aadddsdsfId
erp          //erp
tmsManager   //商贸通管理端
api:【接口名】|【app:<版本号>|wx:<appId>|erp|tmsmanager】  //api接口调用,如：api:getproducts:app:4.0.0

$info是日志内容，各字段用|分割，字段内容根据$tag有不同的内容：
对于api：用户编号、请求参数（json）、返回状态（成功|失败）
对于wx：用户信息、openId、页面、动作
#对于app：用户信息、设备信息、页面、动作
*/

class LogUtils {
	
	// 是否调试模式, 线上需要关闭, true|打开, false|关闭
	const DEBUG = false;
	
	/**
	 * 打印日志
	 * 
	 * @param string $msg        	
	 */
	public static function log($msg) {
		if (self::DEBUG === true) {
			printf ( "debug_log:%s - %s\n", date ( 'Y-m-d H:i:s', time () ), $msg );
		}
	}
	
	/*
	LOG_ALERT	action must be taken immediately
	LOG_CRIT	critical conditions
	LOG_ERR		error conditions
	LOG_WARNING	warning conditions
	LOG_NOTICE	normal, but significant, condition
	LOG_INFO	informational message
	LOG_DEBUG	debug-level message
	*/
	
	/**
	 * 打印alert日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logAlert($msg, $category = 'LOG_ALERT') {
		self::log ( $msg );
		
		openlog($category, LOG_PID, LOG_LOCAL0);
		syslog(LOG_ALERT, $msg);
		closelog();
	}
	
	/**
	 * 打印crit日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logCrit($msg, $category = 'LOG_CRIT') {
		self::log ( $msg );
		
		openlog($category, LOG_PID, LOG_LOCAL0);
		syslog(LOG_CRIT, $msg);
		closelog();
	}
	
	/**
	 * 打印error日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logError($msg, $category = 'LOG_ERR') {
		self::log ( $msg );
		
		openlog($category, LOG_PID | LOG_PERROR, LOG_LOCAL0);
		syslog(LOG_ERR, $msg);
		closelog();
	}
	
	/**
	 * 打印warning日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logWarning($msg, $category = 'LOG_WARNING') {
		self::log ( $msg );
		
		openlog($category, LOG_PID, LOG_LOCAL0);
		syslog(LOG_WARNING, $msg);
		closelog();
	}
	
	/**
	 * 打印notice日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logNotice($msg, $category = 'LOG_NOTICE') {
		self::log ( $msg );
		
		openlog($category, LOG_PID, LOG_LOCAL0);
		syslog(LOG_NOTICE, $msg);
		closelog();
	}
	
	/**
	 * 打印info日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logInfo($msg, $category = 'LOG_INFO') {
		self::log ( $msg );
		
		openlog($category, LOG_PID , LOG_LOCAL0);
		syslog(LOG_INFO, $msg);
		closelog();
	}
	
	/**
	 * 打印debug日志
	 * 
	 * @param string $msg        	
	 * @param string $category        	
	 */
	public static function logDebug($msg, $category = 'LOG_DEBUG') {
		self::log ( $msg );
		
		openlog($category, LOG_PID , LOG_LOCAL0);
		syslog(LOG_DEBUG, $msg);
		closelog();
	}
}