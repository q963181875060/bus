<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>巴士</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <script src="js/hm.js"></script><script type="text/javascript" src="js/jquery-2.js"></script>
    <script type="text/javascript" src="js/jquery-form.js"></script>
    <script type="text/javascript" src="js/func.js"></script>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <link href="./css/style.css" rel="stylesheet" type="text/css">
    <!--swiper-->
    <link href="./css/swiper.css" rel="stylesheet" type="text/css">
	<?php
		include "logicController.php";
		$jssdk = new JSSDK($appid, $secret);
		$signPackage = $jssdk->GetSignPackage();
	?>
    <script>
		var tmp_req_url = 'clientController.php';
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
								
								var ajax_request = $.ajax({
									type        : 'post',
									url         : tmp_req_url,
									timeout     : AJAX_TIMEOUT, 
									async		: true,
									data       : { 'request'   : JSON.stringify(post_data) },
									dataType    : 'json',
									success     : function(data) {
										if(data['suc'] != 1){
											alert(data['msg']);
										}else{
											//alert(data['msg']);
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

<!--<div class="header"></div>-->

<div class="swiper-container swiper-container-horizontal">
    <div class="swiper-wrapper" style="transition-duration: 0ms; transform: translate3d(-1366px, 0px, 0px);">
        <div class="swiper-slide swiper-slide-prev" style="width: 1366px;">
                <a href="###"><img src="pic/59352bc4ac1a1.jpg" alt=""></a>
            </div><div class="swiper-slide swiper-slide-active" style="width: 1366px;">
                <a href="###"><img src="pic/592bea665036a.gif" alt=""></a>
            </div>    </div>

    <div class="swiper-pagination swiper-pagination-clickable"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span></div>
</div>

<script type="text/javascript" src="js/swiper.js"></script>
<script type="text/javascript">
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        autoplay: 5000,
        speed: 800
    });
</script>

<div class="container">

    <div class="location-picker">
        <table>
            <tbody><tr>
                <td class="text-center" style="width:8%"><img src="pic/from_location.png" alt=""></td>
                <td onclick="goto_select_city(0)" style="padding-left:5px">
                    <b class="arrow">&gt;</b>

                    <p style="margin-bottom:5px" id="from_city">
						<?php
							if(isset($_SESSION['from_city'])){
								echo $_SESSION['from_city'];
							}else{
								$_SESSION['from_city'] = '青岛';
								echo $_SESSION['from_city'];
							}
						?> 
					</p>
                    <span>出发城市</span>
                </td>

                <td rowspan="2" class="text-center" style="width:16%;padding: 0 10px" onclick="changeLocation()">
                    <img src="pic/location_change.png" alt="">
                </td>
            </tr>
            <tr>
                <td class="text-center"><img src="pic/to_location.png" alt=""></td>
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
                <td class="text-center" style="padding: 0 5px"><img src="pic/date_picker.png" alt=""></td>
                <td colspan="2">
                    <b class="arrow">&gt;</b>

                    <div class="hide-select-1">
                        <select name="yuding_date">
							<?php
								date_default_timezone_set('Asia/Shanghai');
								$weekarray=array("日","一","二","三","四","五","六");							
								for($i=0;$i<7;$i++){
									echo '<option value="'.date('Y-m-d',strtotime('+'.$i.' day')).'" '. (($i==0)?'selected="selected"':'').'>'.
											date('Y-m-d',strtotime('+'.$i.' day')).' (星期'.$weekarray[date('w',strtotime('+'.$i.' day'))].')'.
										'</option>';
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
                <td style="width:30px"><img src="pic/Notice.png" alt=""></td>
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

    <div class="history_order" style="display:none;">
        <div class="history-title">快捷订票</div>
        <ul class="history-list">
            <li>
                    <a href="http://guangyunbus.com/index.php/Index/step3/from_city/1/to_city/2/from_zhan/128/to_zhan/18">
                        <table>
                            <tbody><tr>
                                <td class="text-center" style="width:8%"><img src="pic/start.png" alt=""></td><td>
                            </td><td>深大地铁站A3出口</td>
                            </tr>
                            <tr>
                                <td class="text-center" style="width:8%"><img src="pic/end.png" alt=""></td><td>
                            </td><td>万胜围地铁口（海珠区）</td>
                            </tr>
                            </tbody></table>
                    </a>
                </li><li>
                    <a href="http://guangyunbus.com/index.php/Index/step3/from_city/2/to_city/1/from_zhan/6/to_zhan/8">
                        <table>
                            <tbody><tr>
                                <td class="text-center" style="width:8%"><img src="pic/start.png" alt=""></td><td>
                            </td><td>客村地铁B出口（海珠区）</td>
                            </tr>
                            <tr>
                                <td class="text-center" style="width:8%"><img src="pic/end.png" alt=""></td><td>
                            </td><td>南山区/世界之窗</td>
                            </tr>
                            </tbody></table>
                    </a>
                </li>        </ul>
    </div>

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
			url         : tmp_req_url,
			timeout     : AJAX_TIMEOUT, 
			async		: false,
			data       : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				window.location.href=data['url'];
			},
			error		:function(err){
				alert("err:"+err);
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
			url         : tmp_req_url,
			timeout     : AJAX_TIMEOUT, 
			async		: false,
			data       : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				window.location.href=data['url'];
			},
			error		:function(err){
				alert("err:"+err);
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

<script>
    (function($){
        $.fn.FontScroll = function(options){
            var d = {time: 3000,s: 'fontColor',num: 1}
            var o = $.extend(d,options);

            this.children('ul').addClass('line');
            var _con = $('.line').eq(0);
            var _conH = _con.height(); //滚动总高度
            var _conChildH = _con.children().eq(0).height();//一次滚动高度
            var _temp = _conChildH;  //临时变量
            var _time = d.time;  //滚动间隔
            var _s = d.s;  //滚动间隔


            _con.clone().insertAfter(_con);//初始化克隆

            //样式控制
            var num = d.num;
            var _p = this.find('li');
            var allNum = _p.length;

            _p.eq(num).addClass(_s);


            var timeID = setInterval(Up,_time);
            this.hover(function(){clearInterval(timeID)},function(){timeID = setInterval(Up,_time);});

            function Up(){
                _con.animate({marginTop: '-'+_conChildH});
                //样式控制
                _p.removeClass(_s);
                num += 1;
                _p.eq(num).addClass(_s);

                if(_conH == _conChildH){
                    _con.animate({marginTop: '-'+_conChildH},"normal",over);
                } else {
                    _conChildH += _temp;
                }
            }
            function over(){
                _con.attr("style",'margin-top:0');
                _conChildH = _temp;
                num = 1;
                _p.removeClass(_s);
                _p.eq(num).addClass(_s);
            }
        }
    })(jQuery);

    $('#FontScroll').FontScroll({time: 5000,num: 1});

</script>

<?php
include 'nav_bar.php';
?>


</body></html>