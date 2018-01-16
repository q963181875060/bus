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
    
</head>
<body>
<div id="lock" style="display: none">
    <div id="shadow_loading"></div><div id="shadow_bg"></div>
</div>

<div class="header">
    <div class="back"><a href="javascript:void(0)" onclick="window.history.back()">&lt; 返回</a></div>
    <div class="header-title">选择站点</div>
</div>


<div class="member-block-width-bottom">
    <ul class="list">
                    </ul>
</div>

<p style="font-size:18px;margin:10px 0 0 10px;text-align:left">选择上下车站点<span style="color:#eb7c1a">（点击站点）</span></p>

<div class="station-select">
    <table class="stations">
        <tbody>
		
<?php
	include 'logicController.php';
	$data = get_select_stop_data();
	$from_stops = array();
	$to_stops = array();
	
	foreach($data as $route){
		$tmp_from_stops = split(">",$route['from_stops']);
		$tmp_to_stops = split(">",$route['to_stops']);
		foreach($tmp_from_stops as $tmp_from_stop){
			if(!isset($from_stops[$tmp_from_stop])){
				$from_stops[$tmp_from_stop] = array();
			}
			foreach($tmp_to_stops as $tmp_to_stop){			
				$from_stops[$tmp_from_stop][$tmp_to_stop] = '';
				$to_stops[$tmp_to_stop] = '';
			}
		}
	}
	
	foreach($from_stops as $key=>$value){
		echo 
		'<tr onclick="selectFromStation(this,\''.$key.'\')">
			<td style="width:20px;text-align:center;padding: 0 5px" class="from_zhan">
			</td>
			<td style="width:20px;text-align:center;padding: 0 5px">
				<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/up_start.png" alt="">
			</td>
			<td>'.$key.'</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/spera-1.png" alt="">
			</td>
			<td></td>
		</tr>';
	}

?>
	</tbody>
	</table>
	
	<table class="stations" id="to_stops_table">
        <tbody>
<?php
	foreach($to_stops as $key=>$value){
		echo 
		'<tr onclick="selectToStation(this,\''.$key.'\')">
			<td style="width:20px;text-align:center;padding: 0 5px" class="to_zhan">
			</td>
			<td style="width:20px;text-align:center;padding: 0 5px">
				<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/down.png" alt=""> 
			</td>
			<td>'.$key.'</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/spera-1.png" alt="">
			</td>
			<td></td>
		</tr>';
	}
?>
		</tbody>
	</table>
   
    <form action="/index.php/Index/step3" id="form">
        <input name="from_zhan" value="" type="hidden">
        <input name="to_zhan" value="" type="hidden">
    </form>
</div>
<?php
	if(count($from_stops) != 0){
		echo '<div style="background:#ddd;padding:20px 10px">
				<span class="button" onclick="lineSearch()">查 询</span>
			</div>';
	}else{
		echo '无班次安排，请选择其他日期';
	}
	
?>


<script type="text/javascript">
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
	var from_stops = JSON.parse('<?php echo json_encode($from_stops); ?>');

    function selectFromStation(op, from_zhan) {
        $('.from_zhan').html('');

        $(op).find('.from_zhan').html('<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/station_select.png" alt="" />');

        $('input[name="from_zhan"]').val(from_zhan);
		
		var html ='';
		for(var key in from_stops[from_zhan]){
			html = html + 
			'<tr onclick="selectToStation(this,\''+key+'\')"><td style="width:20px;text-align:center;padding: 0 5px" class="to_zhan">'+
			'</td><td style="width:20px;text-align:center;padding: 0 5px"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/down.png" alt=""></td><td>'+key+'</td></tr>'+
			'<tr><td></td><td style="text-align:center;"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/spera-1.png" alt=""></td><td></td></tr>';
		}
		
		$('#to_stops_table').html(html);
		$('.to_zhan').html('');
		$('input[name="to_zhan"]').val('');
		
    }

    function selectToStation(op, to_zhan) {
        $('.to_zhan').html('');

        $(op).find('.to_zhan').html('<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/station_select.png" alt="" />');

        $('input[name="to_zhan"]').val(to_zhan);
    }

    function lineSearch() {
        var from_zhan = $('input[name="from_zhan"]').val();
        var to_zhan = $('input[name="to_zhan"]').val();

        if ($.trim(from_zhan) == '0' || $.trim(from_zhan) == '') {
            alert('请选择出发站点');
            return false;
        }

        if ($.trim(to_zhan) == '0' || $.trim(to_zhan) == '') {
            alert('请选择到达站点');

            return false;
        }

		var post_data = {};	
		post_data['action'] = 'goto_select_time';
		post_data['from_stop'] = from_zhan;
		post_data['to_stop'] = to_zhan;
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
        //lock();

        //$('#form').submit();
    }
</script>

<?php
include 'nav_bar.php';
?>


</body></html>