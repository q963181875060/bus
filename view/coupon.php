<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>我的代金券</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <script src="js/hm.js"></script><script type="text/javascript" src="js/jquery-2.js"></script>
    <script type="text/javascript" src="js/jquery-form.js"></script>
    <script type="text/javascript" src="js/func.js"></script>
    <link href="css/style.css" type="text/css" rel="stylesheet">
    <!--swiper-->
    <link href="css/swiper.css" rel="stylesheet" type="text/css">
    <script>
        var SITE_URL = "http://guangyunbus.com/";
        var img_upload_url = 'http://guangyunbus.com/res/upload_file.php';
    </script>

	<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?7aa758070a07098e84e9dbec440b7866";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</head>
<body class="member-body">
<div id="lock" style="display: none">
    <div id="shadow_loading"></div><div id="shadow_bg"></div>
</div>

<div class="header">代金券</div>

<?php
	include 'logicController.php';
	$data = get_coupon_data();
?>

<ul class="order">
<?php 
foreach($data as $coupon){
	if($coupon['state'] == '正常'){
		echo '<li class="li_normal" style="background-color:lightsalmon;">';
	}else{
		echo '<li class="li_grey">';
	}
	
	echo '
			<table >
				<thead>
				<tr>
					<td><span class="price" style="font-size:18px">恭喜您获得 ¥
						'.$coupon['coupon_price'].' 代金券
					</span></td>
					<td></td>
					<td></td>
					<td>
						<span class="status_name">
							'.$coupon['state'].'
						</span>
					</td>
				</tr>
				</thead>

				<tbody>
				<tr>
					<td>可用班次</td>
					<td>
						'.((trim($coupon['route_ids'])=='all')?'全部':str_replace('>',' ',$coupon['route_ids'])).'
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>领取时间</td>
					<td>
						'.$coupon['get_time'].'
					</td>
					<td>使用时间</td>
					<td>
						'.((trim($coupon['use_time'])=='')?'未使用':$coupon['use_time']).'
					</td>
				</tr>
				<tr>
					<td>有效期</td>
					<td colspan="3">
						至 '.$coupon['end_time'].'
					</td>
					<td></td>
					<td></td>
				</tr>';
	if($coupon['state'] == '正常'){
		echo '
				<tr><td colspan="4">
					<img src="pic/gantan.png" style="width:20px;vertical-align:middle" alt=""> <span style="color:#ff5500">可在购票页面勾选使用代金券</span></td>
				</tr>';
	}
	echo	'</tbody>
			</table>	
		</li>';
	
}
?>

</ul>
<div class="pages">
    <ul></ul></div>


<?php
include 'nav_bar.php';
?>

</body>
</html>