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
    <div class="schedule-select-prev">
        <a onclick="select_another_day('pre')">前一天</a>
    </div>
    <img src="pic/image_sequence.png" alt="" style="width:14px;vertical-align:middle"> 
	
	
<?php
	include 'logicController.php';
	$data = get_select_time_data();
	$routes = $data['routes'];
	echo $data['start_date'];
?>
    <div class="schedule-select-next">
		<a onclick="select_another_day('next')">后一天</a>    
	</div>
</div>

<script type="text/javascript">
	var tmp_req_url = 'http://139.199.105.54/bus/controller/clientController.php';
	var AJAX_TIMEOUT = 2000;
	
	function select_another_day(offset){
		var post_data = {};	
		post_data['action'] = 'goto_select_time';
		post_data['change_date'] = offset;//pre or next
		
		$.ajax({
            type        : 'post',
            url         : tmp_req_url,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
	
	function select_time(from_time){
		var post_data = {};	
		post_data['action'] = 'goto_book';
		post_data['from_time'] = from_time;
		
		$.ajax({
            type        : 'post',
            url         : tmp_req_url,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
</script>


<ul class="schedule-list">

<?php

for($i=0;$i<count($routes);$i++){
	echo '
    <li>
        <table>
			<tbody>
				<tr>
                    <td style="width:10%;padding: 0 7px" class="text-center"><img src="pic/clock.png" alt=""></td><td>
                    </td>
                    <td>'.$routes[$i]['from_time'].'</td>
                    <td rowspan="3" class="text-right">
						<p>
							<a href="" class="special_order">
                                    特价¥0                              
                            </a>
							<br><br>
							<a onclick="select_time(\''.$routes[$i]['from_time'].'\')" class="normal_order">
                                    正价¥'.$routes[$i]['price'].'                               
							</a>  
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="pic/start.png" alt=""></td><td>
                    </td><td>'.$routes[$i]['from_stop'].'</td>
                </tr>
                <tr>
                    <td class="text-center" style="width:10%"><img src="pic/end.png" alt=""></td><td>
                    </td><td>'.$routes[$i]['to_stop'].'</td>
                </tr>
            </tbody>
		</table>
    </li>';
}
?>
</ul>

<div class="navigation-bar">
    <div>
        <a href="http://guangyunbus.com/index.php/Index/index">
            <p><img src="pic/home.png" alt=""></p>
            <p>首页</p>
        </a>
    </div>

	<!--<div>
        <a href="/index.php/User/share">
            <p><img src="/Public/app/images/share.png" alt="" /></p>
            <p>分享</p>
        </a>
    </div>-->


	
   <!-- <div>
        <a href="/index.php/Page/show/id/1">
            <p><img src="/Public/app/images/about.png" alt="" /></p>
            <p>关于</p>
        </a>
    </div>

    <div>
        <a href="/index.php/Page/show/id/2">
            <p><img src="/Public/app/images/contact.png" alt="" /></p>
            <p>联系</p>
        </a>
    </div>-->

    <div>
        <a href="http://guangyunbus.com/index.php/User/orders">
            <p><img src="pic/order.png" alt=""></p>
            <p>订单</p>
        </a>
    </div>

    <div>
        <a href="http://guangyunbus.com/index.php/Page/show/id/3">
            <p><img src="pic/icon05.png" alt=""></p>
            <p>指南</p>
        </a>
    </div>
    
    <div>
        <a href="http://guangyunbus.com/index.php/User/index">
            <p><img src="pic/member.png" alt=""></p>
            <p>我的</p>
        </a>
    </div>

</div>

</body></html>