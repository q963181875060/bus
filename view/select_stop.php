<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>选择上下车地点</title>
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
    <div class="header-title">选择站点</div>
</div>


<div class="member-block-width-bottom">
    <ul class="list">
                    </ul>
</div>

<p style="font-size:18px;margin:10px 0 0 10px;text-align:left">选择上下车站点<span style="color:#eb7c1a">（点击站点）</span></p>
<?php
include 'logicController.php';
$data = get_select_stop_data();
$from_stops = $data['from_stops'];
$to_stops = $data['to_stops'];
?>
<div class="station-select">
    <table class="stations">
        <tbody>
		
<?php
	for($i=0; $i<count($from_stops);$i++){
		echo 
		'<tr onclick="selectFromStation(this,\''.$from_stops[$i].'\')">
			<td style="width:20px;text-align:center;padding: 0 5px" class="from_zhan">
			</td>
			<td style="width:20px;text-align:center;padding: 0 5px">
				<img src="pic/up_start.png" alt="">
			</td>
			<td>'.$from_stops[$i].'</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<img src="pic/spera-1.png" alt="">
			</td>
			<td></td>
		</tr>';
	}

?>
		
        <tr>
            <td></td>
            <td style="text-align:center;">
                <img src="pic/spera-1.png" alt="">
            </td>
            <td></td>
        </tr>
		
<?php
	for($i=0; $i<count($to_stops);$i++){
		echo 
		'<tr onclick="selectToStation(this,\''.$to_stops[$i].'\')">
			<td style="width:20px;text-align:center;padding: 0 5px" class="to_zhan">
			</td>
			<td style="width:20px;text-align:center;padding: 0 5px">
				<img src="pic/down.png" alt=""> 
			</td>
			<td>'.$to_stops[$i].'</td>
		</tr>
		<tr>
			<td></td>
			<td style="text-align:center;">
				<img src="pic/spera-1.png" alt="">
			</td>
			<td></td>
		</tr>';
	}

?>
		
    </tbody></table>

    <form action="/index.php/Index/step3" id="form">
        <input name="from_zhan" value="" type="hidden">
        <input name="to_zhan" value="" type="hidden">
    </form>
</div>

<div style="background:#ddd;padding:20px 10px">
    <span class="button" onclick="lineSearch()">查 询</span>
</div>

<script type="text/javascript">
	var tmp_req_url = 'http://139.199.105.54/bus/controller/clientController.php';
	var AJAX_TIMEOUT = 2000;
	

    function selectFromStation(op, from_zhan) {
        $('.from_zhan').html('');

        $(op).find('.from_zhan').html('<img src="pic/station_select.png" alt="" />');

        $('input[name="from_zhan"]').val(from_zhan);
    }

    function selectToStation(op, to_zhan) {
        $('.to_zhan').html('');

        $(op).find('.to_zhan').html('<img src="pic/station_select.png" alt="" />');

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
            url         : tmp_req_url,
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