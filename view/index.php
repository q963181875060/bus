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
		$jssdk = new JSSDK($appid, $secret);
		$signPackage = $jssdk->GetSignPackage();
		
	?>
    <script>
		var SERVER_URL = 'clientController.php';
		var AJAX_TIMEOUT = 2000;

		var apiList = [
			  // 所有要调用的 API 都要加到这个列表中
			  'getLocation',
			  'onMenuShareTimeline',
			  'chooseWXPay'
			];
		var apiCheckList;
        wx.config({
			debug: false,
			appId: '<?php echo $signPackage["appId"];?>',
			timestamp: <?php echo $signPackage["timestamp"];?>,
			nonceStr: '<?php echo $signPackage["nonceStr"];?>',
			signature: '<?php echo $signPackage["signature"];?>',
			jsApiList: apiList
		 });
		 wx.ready(function () {
			 wx.checkJsApi({
				jsApiList: apiList, // 需要检测的JS接口列表，所有JS接口列表见附录2,
				success: function(res) {
					// 以键值对的形式返回，可用的api值true，不可用为false
					// 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
					
					apiCheckList = res['checkResult'];
					if(apiCheckList['getLocation']){
						wx.getLocation({
							type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
							success: function (res) {
								//上传位置到服务器
								var post_data = {};
								post_data['action'] = 'upload_position';
								post_data['latitude'] = res.latitude; // 纬度，浮点数，范围为90 ~ -90
								post_data['longitude'] = res.longitude; // 纬度，浮点数，范围为90 ~ -90
								post_data['speed'] = res.speed; // 速度，以米/每秒计
								
								var ajax_request = $.ajax({
									type        : 'post',
									url         : SERVER_URL,
									timeout     : AJAX_TIMEOUT, 
									async		: true,
									data       : { 'request'   : JSON.stringify(post_data) },
									dataType    : 'json',
									success     : function(data) {
										if(data['suc'] != 1){
											//alert(data['msg']);
										}else{
											//alert('suc');
										}
									}
								})
							}
						});
					}
				}
			});
		  });
    </script>
</head>
<body style="padding-top: 0">
<div id="lock" style="display: none">
    <div id="shadow_loading"></div><div id="shadow_bg"></div>
</div>

<div><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/index_banner4.gif" alt=""></div>
<div class="container">

    <div class="location-picker">
        <table>
            <tbody><tr>
                <td class="text-center" style="width:8%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/from_location.png" alt=""></td>
                <td onclick="goto_select_city(0)" style="padding-left:5px">
                    <b class="arrow">&gt;</b>

                    <p style="margin-bottom:5px" id="from_city">
						<?php
							if(isset($_SESSION['from_city'])){
								echo $_SESSION['from_city'];
							}else{
								$_SESSION['from_city'] = '烟台';
								echo $_SESSION['from_city'];
							}
						?> 
					</p>
                    <span>出发城市</span>
                </td>

                <td rowspan="2" class="text-center" style="width:16%;padding: 0 10px" onclick="changeLocation()">
                    <img src="http://bus-1251514843.cosbj.myqcloud.com/bus/location_change.png" alt="">
                </td>
            </tr>
            <tr>
                <td class="text-center"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/to_location.png" alt=""></td>
                <td onclick="goto_select_city(1)" style="padding-left:5px">
                    <b class="arrow">&gt;</b>

                    <p style="margin-bottom:5px" id="to_city">
						<?php
							if(isset($_SESSION['to_city'])){
								echo $_SESSION['to_city'];
							}else{
								$_SESSION['to_city'] = '请选择';
								echo $_SESSION['to_city'];
							}
						?> 
					</p>
                    <span>到达城市</span>
                </td>
            </tr>

            <tr>
                <td class="text-center" style="padding: 0 5px"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/date_picker.png" alt=""></td>
                <td colspan="2">
                    <b class="arrow">&gt;</b>

                    <div class="hide-select-1">
                        <select name="yuding_date">
							<?php
								date_default_timezone_set('Asia/Shanghai');
								$weekarray=array("日","一","二","三","四","五","六");							
								$num_dates = 0;
								for($i=0;$i<7;$i++){
									if(strtotime('+'.$i.' day') >= strtotime('2018-01-19')){
										echo '<option value="'.date('Y-m-d',strtotime('+'.$i.' day')).'" '. (($i==0)?'selected="selected"':'').'>'.
											date('Y-m-d',strtotime('+'.$i.' day')).' (星期'.$weekarray[date('w',strtotime('+'.$i.' day'))].')'.
										 '</option>';
										 $num_dates++;
										 if($num_dates == 3) break;
									}
								}
							?>
                                                  
						</select>
                    </div>
                </td>
            </tr>
        </tbody></table>
    </div>

    <div style="margin: 10px 0 10px 0">
        <span class="button" onclick="line_search()">查&nbsp;询</span>
    </div>

    <div class="clear"></div>

    <div class="notice" style="display:none">
        <table>
            <tbody><tr>
                <td style="width:30px"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/Notice.png" alt=""></td>
                <td style="padding-left:10px">
                    <div id="FontScroll">
                        <ul class="line" style="margin-top:0">
                            <li><span><a href="http://guangyunbus.com/index.php/News/show/id/1">点击查看订票须知</a></span></li>                        </ul><ul class="line">
                            <li class="fontColor"><span><a href="http://guangyunbus.com/index.php/News/show/id/1">点击查看订票须知</a></span></li>                        </ul>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <!--<div class="history_order">
        <div class="history-title">快捷订票</div>
        <ul class="history-list">
            <li>
                    <a href="http://guangyunbus.com/index.php/Index/step3/from_city/1/to_city/2/from_zhan/128/to_zhan/18">
                        <table>
                            <tbody><tr>
                                <td class="text-center" style="width:8%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/start.png" alt=""></td><td>
                            </td><td>深大地铁站A3出口</td>
                            </tr>
                            <tr>
                                <td class="text-center" style="width:8%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/end.png" alt=""></td><td>
                            </td><td>万胜围地铁口（海珠区）</td>
                            </tr>
                            </tbody></table>
                    </a>
                </li><li>
                    <a href="http://guangyunbus.com/index.php/Index/step3/from_city/2/to_city/1/from_zhan/6/to_zhan/8">
                        <table>
                            <tbody><tr>
                                <td class="text-center" style="width:8%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/start.png" alt=""></td><td>
                            </td><td>客村地铁B出口（海珠区）</td>
                            </tr>
                            <tr>
                                <td class="text-center" style="width:8%"><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/end.png" alt=""></td><td>
                            </td><td>南山区/世界之窗</td>
                            </tr>
                            </tbody></table>
                    </a>
                </li>        </ul>
    </div>-->

</div>


<script type="text/javascript">
	
	function goto_select_city(city_direction){
		if(city_direction == 1 && $('#from_city').html() == '' || $('#from_city').html() == '请选择'){
			alert('请先选择出发城市！');
			return;
		}
		var post_data = {};
		post_data['action'] = 'goto_select_city';
		post_data['city_direction'] = city_direction;
		post_data['from_city'] = $('#from_city').html();
		
		var ajax_request = $.ajax({
			type        : 'post',
			url         : SERVER_URL,
			timeout     : AJAX_TIMEOUT, 
			async		: false,
			data       : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				window.location.href=data['url'];
			},
			error		:function(err){
				alert("err:"+JSON.stringify(err));
			}
		})
	}
	
	
    function line_search() {
        var from_city = $.trim($('#from_city').html());
        var to_city = $.trim($('#to_city').html());
        var start_date = $.trim($('select[name="yuding_date"]').val());

        if (from_city == '请选择') {
            alert('请选择出发城市！');

            return false;
        }

        if (to_city == '请选择') {
            alert('请选择到达城市！');

            return false;
        }

        if (start_date == '') {
            alert('请选择时间！');

            return false;
        }
		var post_data = {};
		post_data['action'] = 'goto_select_stop';
		post_data['from_city'] = from_city;
		post_data['to_city'] = to_city;
		post_data['start_date'] = start_date;
        var ajax_request = $.ajax({
			type        : 'post',
			url         : SERVER_URL,
			timeout     : AJAX_TIMEOUT, 
			async		: false,
			data       : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				if(data['suc'] == 1){
					window.location.href=data['url'];
				}else{
					alert(data['msg']);
				}
			},
			error		:function(err){
				alert("err:"+JSON.stringify(err));
			}
		})
    }

    function changeLocation() {
        var from_city = $('#from_city').html();
        var to_city = $('#to_city').html();

        $('#from_city').html(to_city);
        $('#to_city').html(from_city);
        //$.get("/index.php/Index/change");
    }

</script>
<?php
include 'nav_bar.php';
?>
</body></html>