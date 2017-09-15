<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>选择时间点</title>
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
    
    <img src="pic/image_sequence.png" alt="" style="width:14px;vertical-align:middle"> 		
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
include 'logicController.php';
$routes = get_select_time_data();
if(count($routes) == 0){
	echo '无班次安排，请选择其他日期';
}
foreach($routes as $route_id=>$route){
	echo '
    <li>
        <table>
			<tbody>
				<tr>
                    <td style="width:10%;padding: 0 7px" class="text-center"><img src="pic/clock.png" alt=""></td><td>
                    </td>
                    <td id="from_time_'.$route_id.'">'.$route['from_time'].'</td>
                    <td rowspan="3" class="text-right">
						<p>
							<a onclick="select_time(1,'.$route_id.','.$route['special_price'].','.$route['available_special_num'].')" class="special_order">
                                    特价¥'.$route['special_price'].'(剩'.$route['available_special_num'].'张)
							</a> 
							<br><br>
							
							<a onclick="select_time(0,'.$route_id.','.$route['price'].','.$route['available_num'].')" class="normal_order">
                                    ¥'.$route['price'].' (剩'.$route['available_num'].'张)
							</a> 
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="pic/start.png" alt=""></td><td>
                    </td><td>'.$_SESSION['from_stop'].'</td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="pic/end.png" alt=""></td><td>
                    </td><td>'.$_SESSION['to_stop'].'</td>
                </tr>
            </tbody>
		</table>
    </li>';
}
?>
</ul>

<script type="text/javascript">
	var tmp_req_url = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
	
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
            url         : tmp_req_url,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
	
	function select_time(type, route_id, price, available_num){
		if(available_num <= 0){
			alert("无余票，请选择其他车次");
			return;
		}
		
		var post_data = {};	
		post_data['action'] = 'goto_book';
		post_data['route_id'] = route_id;
		post_data['from_time'] = $.trim($('#from_time_'+route_id).html());
		post_data['start_date'] = $.trim($('#lookup_date').html());
		post_data['is_special_ticket'] = type;
		post_data['price'] = price;
		
		$.ajax({
            type        : 'post',
            url         : tmp_req_url,
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