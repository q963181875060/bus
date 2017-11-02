<?php
/*
此文件为服务器主动调用微信接口的文件
注意：admin也通过相对路径调用此文件，因此位置不可改变
*/

require_once('jssdk.php');
require_once('common.php');

createMenu();



//这个函数只能够由url直接调用
function createMenu(){
	global $appid, $secret;
	$jssdk = new JSSDK($appid, $secret);
	$accessToken = $jssdk->getAccessToken();
	
	
	$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken;
	$data = '{"button": [
					{
						"type": "view", 
						"name": "购票", 
						"url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2fhelibus.cn%2fbus%2fview%2findex.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect"						
					},
					{
						"type": "view", 
						"name": "我的订单", 
						"url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2fhelibus.cn%2fbus%2fview%2fhistory.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect"						
					},
					{
						"type": "view", 
						"name": "领金券", 
						"url": "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri=http%3a%2f%2fhelibus.cn%2fbus%2fview%2fsurvey.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect"						
					}
				]
			}';
	echo httpPost($url,$data);
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
