<?php
/*
此文件为服务器主动调用微信接口的文件
注意：admin也通过相对路径调用此文件，因此位置不可改变
*/

require_once('jssdk.php');
require_once('common.php');

//sendTemplate();


function send_book_ticket_template($data){
	global $appid, $secret;
	$jssdk = new JSSDK($appid, $secret);
	$accessToken = $jssdk->getAccessToken();
	
	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
	$data = '  {
           "touser":"'.$data['user_id'].'",
           "template_id":"l5ZWuH55inMc7TvjzH0dbroefa0bKdf5eo4om__-FcY",
           "data":{
                   "first": {
                       "value":"'.$data['first'].'",
                       "color":"#173177"
                   },
				   "productType": {
                       "value":"'.$data['productType'].'"
                   },
				   "name": {
                       "value":"'.$data['name'].'"
                   },
				   "time": {
                       "value":"'.$data['time'].'"
                   },
				   "result": {
                       "value":"'.$data['result'].'"
                   },
				   "remark": {
                       "value":"'.$data['remark'].'"
                   }
           }
       }';
	httpPost($url,$data);
}

function send_cancel_ticket_template($data){
	global $appid, $secret;
	$jssdk = new JSSDK($appid, $secret);
	$accessToken = $jssdk->getAccessToken();
	
	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
	$data = '  {
           "touser":"'.$data['user_id'].'",
           "template_id":"hbnz0B3ws3q42XO-qgJg2ogGoYc1jQOWWdxk2HaXbdA",
           "data":{
                   "first": {
                       "value":"'.$data['first'].'",
                       "color":"#173177"
                   },
				   "keyword1": {
                       "value":"'.$data['keyword1'].'"
                   },
				   "keyword2": {
                       "value":"'.$data['keyword2'].'"
                   },
				   "keyword3": {
                       "value":"'.$data['keyword3'].'"
                   },
				   "remark": {
                       "value":"'.$data['remark'].'"
                   }
           }
       }';
	httpPost($url,$data);
}

function send_get_coupon_template($data){
	global $appid, $secret;
	$jssdk = new JSSDK($appid, $secret);
	$accessToken = $jssdk->getAccessToken();
	
	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
	$data = '  {
           "touser":"'.$data['user_id'].'",
           "template_id":"adTzgdsVsiYdbFOGvw6H9RKTfn0Dhkfz6D7JSESWmQ8",
           "data":{
                   "first": {
                       "value":"'.$data['first'].'",
                       "color":"#173177"
                   }
           }
       }';
	httpPost($url,$data);
}

function send_admin_template($user_ids, $data){
	global $appid, $secret;
	$jssdk = new JSSDK($appid, $secret);
	$accessToken = $jssdk->getAccessToken();
	
	$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
	
	foreach($user_ids as $key=>$user){
		$params = '  {
           "touser":"'.$user.'",
           "template_id":"adTzgdsVsiYdbFOGvw6H9RKTfn0Dhkfz6D7JSESWmQ8",
           "data":{
                   "first": {
                       "value":"'.$data['first'].'",
                       "color":"#173177"
                   }
           }
       }';
		httpPost($url,$params);
	}
}


function httpPost($url, $data) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
	// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
}

?>
