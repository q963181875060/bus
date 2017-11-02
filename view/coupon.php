<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>合力巴士</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/jquery-2.js"></script>
    <script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/jquery-form.js"></script>
    <script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/func.js"></script>
	<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/jweixin-1.0.0.js"></script>
    <link href="http://bus-1251514843.cosbj.myqcloud.com/css/style.css" rel="stylesheet" type="text/css">
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
					<td><span class="price" style="font-size:18px">¥
						'.$coupon['coupon_price'].' 代金券
					</span>
					</td>
					
					<td>
						<span class="status_name">
							'.$coupon['state'].'
						</span>
					</td>
				</tr>
				</thead>

				<tbody>
				<tr>
					<td>说明</td>
					<td>
						'.$coupon['coupon_describe'].'
					</td>
					
				</tr>
				<!--<tr>
					<td>领取时间</td>
					<td>
						'.$coupon['get_time'].'
					</td>
					<td>使用时间</td>
					<td>
						'.((trim($coupon['use_time'])=='')?'未使用':$coupon['use_time']).'
					</td>
				</tr>-->
				<tr>
					<td>有效期</td>
					<td colspan="3">
						至 '.$coupon['end_time'].'
					</td>
					
				</tr>';
	if($coupon['state'] == '正常'){
		echo '
				<tr><td colspan="4">
					<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/gantan.png" style="width:20px;vertical-align:middle" alt=""> <span style="color:#ff5500">可在购票页面勾选使用代金券</span></td>
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