<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>提交预定</title>
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
    <div class="back"><a href="javascript:history.go(-1)">&lt; 返回</a></div>
    <div class="header-title">车票预订</div>
</div>


<form id="form1" method="post" action="/index.php/Index/do_post">
    <ul class="ticker-order">
        <li>
            <span>线路：</span>
			<?php
				echo $_SESSION['from_city'];
			?>
             &nbsp;
            <img src="pic/arrow_right_black.png" alt="" style="width:50px">
            &nbsp; 
			<?php
				echo $_SESSION['to_city'];
			?>			
		</li>
        <li>
            <span>上车：</span>
            <?php
				echo $_SESSION['from_stop'];
			?>		
		</li>
		<li>
            <span>下车：</span>
            <?php
				echo $_SESSION['to_stop'];
			?>		
		</li>
        <li>
            <span>时间：</span>
			<?php
				echo $_SESSION['start_date'].'&nbsp;'.$_SESSION['from_time'];
			?>
			<div id="is_expire" style="display:none;">
			<?php
				date_default_timezone_set('Asia/Shanghai');
				if(strtotime($_SESSION['start_date'].' '.$_SESSION['from_time']) < time()){
					echo "1";
				}else{
					echo "0";
				}
			?>
			</div>
		</li>

        <li>
                <span>单张票价：</span>
                <b class="price" style="font-size:22px;font-weight:500">¥
				<?php
					echo $_SESSION['price'];
				?>
				</b>
				<div id="price" style="display:none;">
				<?php
					echo $_SESSION['price'];
				?>
				</div>
            </li>
            <li>
                <span>票数：</span>
                <img src="pic/image_btn_add_press.png" alt="" style="width:28px" onclick="minus()">
                <input name="num" id="num" readonly="readonly" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px;text-align:center;font-size:18px;vertical-align:middle" value="0" size="3" type="text">
                <img src="pic/image_btn_add.png" alt="" style="width:28px" onclick="plus()">
            </li>
          
	</ul>

    <ul class="ticker-order">


        <!--<li>
            <p style="margin-bottom:10px"><span>支付方式：</span></p>
            <table>
                <tbody>
                    <tr>
                        <td style="padding:10px 0">
                            <img src="pic/image_weixinpay.png" alt="" style="width:30px">
                            微信支付
                        </td>
                        <td class="text-right"><input name="payment_id_radio" id="payment_id_1" checked="checked" type="radio"></td>
                    </tr>
                 </tbody>
			</table>
        </li>-->
		<li>
            <p style="margin-bottom:10px"><span>使用代金券：</span></p>
            <table>
                <tbody>
				<?php
				include 'logicController.php';
				$coupons = get_book_coupon_data();
				foreach($coupons as $coupon){
					echo '<tr>
                        <td class="price" style="padding:10px 0;font-weight:300">
                            ¥ '.$coupon['coupon_price'].' 代金券
                        </td>
                        <td class="text-right"><input name="coupon_ratio" onclick="select_coupon(\''.$coupon['user_coupon_id'].':'.$coupon['coupon_price'].'\')" id="user_coupon_'.$coupon['user_coupon_id'].'" type="radio"></td>
                    </tr>';
				}
				
				?>
                 </tbody>
			</table>
        </li>
        <li style="display:none">
            <p><span>预订人信息：</span></p>
            <p>
            <span>联系人：</span>
                <input name="username" id="username" class="input" placeholder="姓名" value="刘加贺" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
            </p>

            <p>
            <span>手机号：</span>
                <input name="phone" id="phone" class="input" placeholder="手机号码" value="13120361231" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
            </p>
        </li>
        <li id="total_price_div" style="">
			<table>
				<tbody><tr>
					<td><span>总价：</span></td>
					<td class="text-right price" style="font-size:22px" id="total_price">¥0</td>
				</tr>
			</tbody></table>
		</li>    
	</ul>
</form>

<div class="navigation-footer" style="border-top: 1px solid #ddd;background: #eee;position: fixed;bottom: 0;left: 0;width: 100%;padding-top: 5px;padding-bottom: 5px;">
    <div class="container">
        <div class="button" onclick="buy_ticket()" data-type="1">提 交 订 单</div>
	</div>
</div>

<div class="notice-box" id="child-tip">
    <div>
        <p>1、1.2米以下（含1.2米及手抱儿童）需购儿童票留位；</p>
        <p>2、1.2米以上请购买成人票；</p>
        <p>3、儿童乘车必须由成人陪伴；</p>
    </div>
    <p onclick="$(this).parent().hide()">知道了</p>
</div>

<div class="notice-box" id="share_success" style="top:45%;">
    <div>
        <p>&nbsp;</p>
        <p>恭喜您获得特价票购买资格，马上提交订单订票吧</p>
        <p>&nbsp;</p>
    </div>
</div>

<div class="notice-box" id="tj_tip" style="top: 30%;">
    <div>
        <p id="tj_tip_text">特价票必须在线支付并且预定成功后不能改签和退票！请确定上车时间后再预订，否则后果自负。自提交订单5分钟内未成功支付，订单将会被自动取消！</p>
    </div>
    <p onclick="$(this).parent().hide()">知道了</p>
</div>

<script type="text/javascript">

	var tmp_req_url = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
	
	var user_coupon_id = -1;
	var coupon_price = 0;
	var total_price = 0;
	var price = 0;
	var num = 0;
	
	plus();
	
	function buy_ticket(){
		if(num == 0){
			alert("请购买至少一张车票");
			return;
		}
		if($.trim($('#is_expire').html()) == '1'){
			if(!window.confirm('此班次的发车时间已过期，确认购买吗？')){
                 return;
            }
		}
		var post_data = {};	
		post_data['action'] = 'book_ticket';
		post_data['ticket_num'] = num;
		if(user_coupon_id != -1){
			post_data['user_coupon_id'] = user_coupon_id;
		}
		post_data['total_price'] = total_price;
		//alert(user_coupon_id);
		$.ajax({
            type        : 'post',
            url         : tmp_req_url,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
				if(data['suc'] == 1){
					window.location.href=data['url'];
				}else{
					alert(data['msg']);
				}
                
            }
        })
	}
	
    function calTotal() {
		price = parseFloat($.trim($('#price').html()));
        total_price = (price * num) - coupon_price;
		if(total_price < 0){
			total_price = 0;
		}
		
        $('#total_price').html('&yen;' + total_price);
    }

    function select_coupon(val) {
        var a = val.split(':');
        user_coupon_id = parseInt(a[0]);
        coupon_price = parseFloat(a[1]);
        calTotal();
    }

    function minus() {
        num = $.trim($('input[name="num"]').val());

        if (num == '') {
            num = 1;
        }

        if (num > 1) {
            num = parseInt(num) - 1;
        }

        $('input[name="num"]').val(num);

        calTotal();
    }

    function plus() {
        num = $.trim($('input[name="num"]').val());
		
        if (num == '') {
            num = 0;
        }

        if (num >= 0) {
            num = parseInt(num) + 1;
        }


        $('input[name="num"]').val(num);

        calTotal();
    }
</script>


</body></html>