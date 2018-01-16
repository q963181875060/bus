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
	<script type="text/javascript">
		// 对浏览器的UserAgent进行正则匹配，不含有微信独有标识的则为其他浏览器
		var useragent = navigator.userAgent;
		if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
			// 这里警告框会阻塞当前页面继续加载
			alert('已禁止本次访问：您必须使用微信内置浏览器访问本页面！');
			// 以下代码是用javascript强行关闭当前页面
			var opened = window.open('about:blank', '_self');
			opened.opener = null;
			opened.close();
		}
	</script>
	<?php
		include "logicController.php";
	?>
	<script>
		var SERVER_URL = 'clientController.php';
		var AJAX_TIMEOUT = 2000;
		
	
	</script>
	
</head>
<body>
<div id="lock" style="display: none">
    <div id="shadow_loading"></div><div id="shadow_bg"></div>
</div>

<div class="header">
    <div class="back"><a href="javascript:void(0)" onclick="window.history.back()">&lt; 返回</a></div>
    <div class="header-title">选择班次</div>
</div>



<div class="schedule-select-block">
    
<?php
	date_default_timezone_set('Asia/Shanghai');
	echo '
		<div class="schedule-select-prev">
			<a onclick="select_another_day(\''.date("Y-m-d",(strtotime($_SESSION['lookup_date']) - 3600*24)).'\')">前一天</a>
		</div>';
?>
    
    <img src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_sequence.png" alt="" style="width:14px;vertical-align:middle"> 		
<?php
	$weekarray=array("日","一","二","三","四","五","六");
	$dayinweeek = ' (星期'.$weekarray[date('w',strtotime($_SESSION['lookup_date']))].')';
	echo $_SESSION['lookup_date'].$dayinweeek;
	
	echo '<div id="lookup_date" style="display:none;">'.$_SESSION['lookup_date'].'</div>';
	echo '
		<div class="schedule-select-next">
			<a onclick="select_another_day(\''.date("Y-m-d",(strtotime($_SESSION['lookup_date']) + 3600*24)).'\')">后一天</a>
		</div>';
?>
</div>

<ul class="schedule-list">

<?php
$routes = get_select_time_data();
if(count($routes) == 0){
	echo '尚未开始发售，只能提前一天预定哦';
}
?>


	
					
					
<?php
foreach($routes as $route_id=>$route){
	echo '<li onclick="select_time('.$route_id.','.$route['available_num'].')">
			<table>
			<tbody>
    		<tr>
                    <td style="width:10%;padding: 0 7px" class="text-center"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/clock.png" alt=""></td><td>
                    </td>
                    <td id="from_time_'.$route_id.'">'.$route['from_time'].'</td>
                    <td rowspan="3" class="text-right">
						<p>';
	if($route['special_ticket_num'] > 0){
						echo '<a class="special_order">
								特价¥'.$route['special_price'];
						if($route['available_special_num'] <= 10){
							echo '(剩'.$route['available_special_num'].'张)';
						}
						
						echo	'</a>
							<br><br>';

	}
		
	echo '			
							<a class="normal_order">
                                    正价¥'.$route['price'];
	if($route['available_num'] <= 10){
							echo '(剩'.$route['available_num'].'张)';
	}
	echo '					</a> 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/start.png" alt=""></td><td>
                    </td><td style="width:50%">'.$_SESSION['from_stop'].'</td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/end.png" alt=""></td><td>
                    </td><td>'.$_SESSION['to_stop'].'</td>
                </tr>
			 </tbody>
		</table>
    </li>';
}
	
?>

           

</ul>

<script type="text/javascript">

	
	function select_another_day(lookup_date){
		var pre_time = new Date(new Date(lookup_date).setHours(0, 0, 0, 0)).getTime();
		var today_time = new Date(new Date().setHours(0, 0, 0, 0)).getTime();
		if(pre_time < today_time){
			alert("不能预定昨天的车票");
			return false;
		}
		
		var post_data = {};	
		post_data['action'] = 'goto_select_time';
		post_data['lookup_date'] = $.trim(lookup_date);
		
		$.ajax({
            type        : 'post',
            url         : SERVER_URL,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
	
	function select_time(route_id, available_num){
		if(available_num <= 0){
			alert("无余票，请选择其他车次");
			return;
		}
		
		var post_data = {};	
		post_data['action'] = 'goto_book';
		post_data['route_id'] = route_id;
		post_data['from_time'] = $.trim($('#from_time_'+route_id).html());
		post_data['start_date'] = $.trim($('#lookup_date').html());
		
		$.ajax({
            type        : 'post',
            url         : SERVER_URL,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
</script>

<?php
include 'nav_bar.php';
?>
</body></html>