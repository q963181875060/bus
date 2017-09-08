<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>我的订单</title>
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

<div class="header">订单</div>

<?php
	include 'logicController.php';
	$data = get_history_data();
?>

<ul class="order">
<?php 
for($i=0;$i<count($data);$i++){
	echo '
		<li class="li_normal">
			<table>
				<thead>
				<tr>
					<td colspan="3" class="from_to">
						'.$data[$i]['from_city'].'
						<img src="pic/arrow.png" style="width:35px;vertical-align:middle" alt="">
						'.$data[$i]['to_city'].'
					</td>
					<td>
						<span class="status_name">
							'.$data[$i]['state'].'
						</span>
					</td>
				</tr>
				</thead>

				<tbody>
					<tr>
						<td>订单编号</td>
						<td>
							'.$data[$i]['book_id'].'
						</td>
						<td>验票码</td>
						<td>
							'.$data[$i]['verify_code'].'
						</td>
					</tr>        <tr>
					<td>乘车站点</td>
					<td colspan="3">
						'.$data[$i]['from_stop'].'
					</td>
				</tr>
				<tr>
					<td>乘车日期</td>
					<td colspan="3">
						'.$data[$i]['start_date'].' '.$data[$i]['from_time'].'
					</td>
				</tr>
				<tr>
					<td>领队电话 </td>
					<td>
						'.$data[$i]['contact_mobile'].'
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
						<td>票数</td>
						<td>
							'.$data[$i]['ticket_num'].'
						</td>
						<td></td>
						<td></td>
				</tr>        
				<tr>
					<td>价格</td>
					<td><span class="price" style="font-size:18px">¥
						'.$data[$i]['price'].'
					</span></td>
					<td style="display:none">支付方式</td>
					<td style="display:none">微信支付
						<b class="status0">[未支付]</b>
					</td>
				</tr>

				<tr><td colspan="4">
					<img src="pic/gantan.png" style="width:20px;vertical-align:middle" alt=""> <span style="color:#ff5500">请您在规定的时间内按时乘车</span></td>
				</tr>        </tbody>
			</table>

			<div class="order-buttons">
				<a href="http://guangyunbus.com/index.php/User/continue_pay/id/1589331">继续支付</a>
				<a class="cancel_order" href="javascript:cancel_order('.$data[$i]['book_id'].')">取消车票</a>        <div class="clear"></div>
			</div>
		</li>
	
	';
}
?>


<li class="li_grey">

    <table>
        <thead>
        <tr>
            <td colspan="3" class="from_to">
                广州                <img src="pic/arrow.png" style="width:35px;vertical-align:middle" alt="">
                深圳            </td>
            <td>
                <span class="status_name">
                                            已过期                </span>
            </td>
        </tr>
        </thead>

        <tbody>
                    <tr>
                <td>订单编号</td>
                <td>1485915</td>
                <td>验票码</td>
                <td>3936</td>
            </tr>        <tr>
            <td>乘车站点</td>
            <td colspan="3">客村地铁B出口（海珠区）</td>
        </tr>
        <tr>
            <td>乘车日期</td>
            <td colspan="3">2017-07-30 09:35</td>
        </tr>
        <tr>
            <td>领队电话 </td>
            <td>13632212647</td>
            <td></td>
            <td></td>
        </tr>
                            <tr>
                <td>票数</td>
                <td>1 张</td>
                <td></td>
                <td></td>
            </tr>        <tr>
            <td>价格</td>
            <td><span class="price" style="font-size:18px">¥35.00</span></td>
            <td>支付方式</td>
            <td>
                微信支付                <b class="status1">[已支付]</b>            </td>
        </tr>

                </tbody>
    </table>

    <div class="order-buttons">
                <div class="clear"></div>
    </div>
</li>
</ul>
<div class="pages">
    <ul></ul></div>


<div class="notice-box" id="notice-box">
    <div>
        <p style="font-size:16px">你确定要取消车票吗？</p>
        <p style="color:#666">注：如已付款，将扣除5%手续费，款会原路退还到您账户，请留意您账户信息；如没收到款请联系客服；</p>
    </div>
    <p class="more-buttons" onclick="cancel_order_do()"><span>确定</span></p>
    <p class="more-buttons" onclick="$(this).parent().hide()">取消</p>
</div>

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
            <p><img src="pic/order_h.png" alt=""></p>
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




<script>

	var tmp_req_url = 'http://139.199.105.54/bus/controller/clientController.php';
	var AJAX_TIMEOUT = 2000;
    var order_id = 0;
    function cancel_order(id) {
        order_id = id;
        $('#notice-box').show();
    }

    function cancel_order_do(){
        if( !(order_id > 0) ) {
            alert('参数错误');
            return false;
        }
        var post_data = {};	
		post_data['action'] = 'cancel_book';
		post_data['book_id'] = order_id;
		
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
</script></body></html>