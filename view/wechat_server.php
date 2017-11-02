<?php
/*
此文件为被动接受微信服务器消息的server
场景为：
-微信服务器的接入认证
-用户进入公众号会话时候的位置上报
-用户给公众号发信息
*/

require_once('common.php');

date_default_timezone_set('Asia/Shanghai');

//error_log("receive");

//获得参数 signature nonce token timestamp echostr
if(isset($_GET['echostr'])){
	$nonce     = $_GET['nonce'];
	$token     = 'token';
	$timestamp = $_GET['timestamp'];
	$echostr   = $_GET['echostr'];
	$signature = $_GET['signature'];
	//形成数组，然后按字典序排序
	$array = array();
	$array = array($nonce, $timestamp, $token);
	sort($array);
	//拼接成字符串,sha1加密 ，然后与signature进行校验
	$str = sha1( implode( $array ) );
	if( $str == $signature && $echostr ){
		//第一次接入weixin api接口的时候
		echo  $echostr;
		exit;
	}
}else{

	//1.获取到微信推送过来post数据（xml格式）
	$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
	//error_log($postArr);
	//2.处理消息类型，并设置回复类型和内容
	/*<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[subscribe]]></Event>
	</xml>*/
	$postObj = simplexml_load_string( $postArr );
	//$postObj->ToUserName = '';
	//$postObj->FromUserName = '';
	//$postObj->CreateTime = '';
	//$postObj->MsgType = '';
	//$postObj->Event = '';
	// gh_e79a177814ed
	//判断该数据包是否是订阅的事件推送
	if(isset($postObj->MsgType) && strtolower( $postObj->MsgType) == 'event'){
		//如果是关注 subscribe 事件
		if( strtolower($postObj->Event) == 'subscribe' ){
			//回复用户消息(纯文本格式)
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$time     = time();
			$msgType  =  'text';
			$content  = '欢迎关注合力巴士，为您提供城际巴士服务。点击下面“领金券”，领取代金券吧！';
			$template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
			$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
			echo $info;
			exit;
			/*<xml>
			<ToUserName><![CDATA[toUser]]></ToUserName>
			<FromUserName><![CDATA[fromUser]]></FromUserName>
			<CreateTime>12345678</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[你好]]></Content>
			</xml>*/
		}else if(strtolower($postObj->Event) == 'location'){//存储用户位置
			try {					
				
				$dbh = new PDO ($DBSTR,$user,$pass); 
				$sql = "insert into position_table (user_id, latitude, longitude, timestamp) values (:user_id, :latitude, :longitude, :timestamp)";
				$exe_params = array();
				$exe_params[':user_id']=trim($postObj->FromUserName);
				$exe_params[':latitude']=trim($postObj->Latitude);
				$exe_params[':longitude']=trim($postObj->Longitude);
				$exe_params[':timestamp']=time();
				$sth = $dbh->prepare($sql);
				$sth->execute($exe_params);
				$affected_rows = $sth->rowCount();
				if($affected_rows == 0){
					throw new Exception("地理位置存储数据库失败");
				}
									
			} catch (Exception $e) {
				$msg = $e->getMessage();
				error_log($msg);
			}finally{
				$dbh = null;
			}
			echo 'success';
			exit;
		}
	}else if( strtolower( $postObj->MsgType) == 'text'){
		
		try {
			
			$user_id   = $postObj->FromUserName;
			$content = $postObj->Content;
			$toUser   = $postObj->FromUserName;
			$fromUser = $postObj->ToUserName;
			$time     = time();
			$msgType  =  'text';
			
			$dbh = new PDO ($DBSTR,$user,$pass); 
			//验票员验票流程
			$sql = "select user_id from user_table where state='已绑定' and user_id=:user_id and role='验票员'";
			$exe_params = array();
			$exe_params[':user_id']=trim($user_id);
			$sth = $dbh->prepare($sql);
			$sth->execute($exe_params);
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			if(count($res) != 0){
				//已确认是验票员发送的信息
				$sql = "update book_table set state='已验票', verify_user_id=:verify_user_id, verify_time=:verify_time where verify_code=:verify_code and start_date=:start_date";
				$exe_params = array();
				$exe_params[':verify_user_id']=trim($user_id);
				$exe_params[':verify_time']=date("Y-m-d H:i:s");
				$exe_params[':verify_code']=trim($content);
				$exe_params[':start_date']=date('Y-m-d');
				$sth = $dbh->prepare($sql);
				$sth->execute($exe_params);
				$affected_rows = $sth->rowCount();
				if($affected_rows == 0){
					//验票员验票失败
					$tocontent  = '不存在此票，验票失败！';
					$template = "<xml>
									<ToUserName><![CDATA[%s]]></ToUserName>
									<FromUserName><![CDATA[%s]]></FromUserName>
									<CreateTime>%s</CreateTime>
									<MsgType><![CDATA[%s]]></MsgType>
									<Content><![CDATA[%s]]></Content>
									</xml>";
					$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $tocontent);
					echo $info;
					throw new Exception("success");
				}else{
					//验票员验票成功
					$tocontent  = '验票成功！';
					$template = "<xml>
									<ToUserName><![CDATA[%s]]></ToUserName>
									<FromUserName><![CDATA[%s]]></FromUserName>
									<CreateTime>%s</CreateTime>
									<MsgType><![CDATA[%s]]></MsgType>
									<Content><![CDATA[%s]]></Content>
									</xml>";
					$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $tocontent);
					echo $info;
					throw new Exception("success");
				}
				
			}
			
			//验票员绑定流程
			$sql = "update user_table set state='已绑定', user_id=:user_id, register_time=:register_time where mobile=:mobile and state='' and role='验票员'";
			$exe_params = array();
			$exe_params[':user_id']=trim($user_id);
			$exe_params[':register_time']=date("Y-m-d H:i:s");
			$exe_params[':mobile']=trim($content);
			$sth = $dbh->prepare($sql);
			$sth->execute($exe_params);
			$affected_rows = $sth->rowCount();
			if($affected_rows != 0){
				//验票员绑定成功，发送成功绑定消息
				$tocontent  = '恭喜绑定验票员成功！';
				$template = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								</xml>";
				$info     = sprintf($template, $toUser, $fromUser, $time, $msgType, $tocontent);
				echo $info;
				throw new Exception("success");
			}
			//非验票员发的文本信息
			echo "success";
								
		} catch (Exception $e) {
			$msg = $e->getMessage();
			//error_log($msg);
		}finally{
			$dbh = null;
			exit;
		}
	}else if(isset($postObj->transaction_id) && isset($postObj->return_code) && strtolower( $postObj->return_code) == 'success'){//微信支付订单回调
		$book_id = (int)$postObj->attach;
		$price = (int)$postObj->settlement_total_fee;
		//暂时不在服务器端做验证
		//error_log("get pay success inform");
		echo '<xml> 
			  <return_code><![CDATA[SUCCESS]]></return_code>
			  <return_msg><![CDATA[OK]]></return_msg>
			</xml> 
			';
		exit;
	}

}
echo "success";
exit;
?>