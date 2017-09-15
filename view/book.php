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

<?php
	include 'logicController.php';
	//$data = get_book_data();
?>

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
				<div id="single_price" style="display:none;">
				<?php
					echo $_SESSION['price'];
				?>
				</div>
                <span style="display:none">（儿童 <b class="price">¥17.5</b> 元）</span>
            </li>
            <li>
                <span>票数：</span>
                <img src="pic/image_btn_add_press.png" alt="" style="width:28px" onclick="minus()">
                <input name="num" id="num" readonly="readonly" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px;text-align:center;font-size:18px;vertical-align:middle" value="0" size="3" type="text">
                <img src="pic/image_btn_add.png" alt="" style="width:28px" onclick="plus()">
            </li>
            <li style="display:none">
                <span>儿童票数：</span>
                <img src="pic/image_btn_add_press.png" alt="" style="width:28px" onclick="child_minus()">
                <input name="child_num" id="child_num" readonly="readonly" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px;text-align:center;font-size:18px;vertical-align:middle" value="0" size="3" type="text">
                <img src="pic/image_btn_add.png" alt="" style="width:28px" onclick="child_plus()">
                <p style="color:#0ea9e2;font-size:12px;float:right" onclick="$('#child-tip').show()">
                    <img src="pic/children_question.png" alt="" style="width:16px;vertical-align:middle"> 儿童票购买标准
                </p>
            </li>
            <li style="display:none">
                <span>交通部规定：儿童或婴儿乘车必须占座位，否则视为超载。请携带儿童或婴儿的乘客自觉购买儿童票留位。</span>
            </li>
                    </ul>

    <ul class="ticker-order">


        <li>
            <p style="margin-bottom:10px"><span>支付方式：</span></p>

            <table>
                <tbody>
					<tr onclick="select_payment(0,35)" style="display:none">
                        <td style="padding:10px 0">
                            <img src="pic/image_pay.png" alt="" style="width:30px">
                            上车支付
                        </td>
                        <td class="text-right"><input name="payment_id_radio" id="payment_id_0" type="radio"></td>
                    </tr>
                    <tr onclick="select_payment(1,35)">
                        <td style="padding:10px 0">
                            <img src="pic/image_weixinpay.png" alt="" style="width:30px">
                            微信支付
                        </td>
                        <td class="text-right"><input name="payment_id_radio" id="payment_id_1" checked="checked" type="radio"></td>
                    </tr>
                             </tbody></table>
        </li>
        <input name="payment_id" id="payment_id" value="1" type="hidden">

                    <input name="coupon_sel" id="coupon_sel" value="0:0" type="hidden">        <input name="coupon_id" id="coupon_id" value="0" type="hidden">
        <input name="coupon_price" id="coupon_price" value="0" type="hidden">


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
                        <td class="text-right price" style="font-size:22px" id="price">¥0</td>
						<div style="display:none;" id="total_price"></div>
                    </tr>
                </tbody></table>
            </li>    </ul>

    <input name="line_id" value="4213" type="hidden">
    <input name="yuding_date" value="2017-09-07" type="hidden">
    <input name="yuding_time" value="13:10" type="hidden">
    <input name="from_zhan" value="2" type="hidden">
    <input name="to_zhan" value="18" type="hidden">
    <input name="price_type" id="price_type" value="1" type="hidden">
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

    //使用优惠券
    var use_coupon = false;

    //票价
    var price = 0;

    //儿童票折扣
    var child_discount = 0.5;

    //最大优惠价格
    var max_coupon_price = 0;

	function buy_ticket(){
		var num = $.trim($('input[name="num"]').val());
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
        var coupon_price = $('#coupon_price').val();
        var num = $.trim($('input[name="num"]').val());
        var child_num = $.trim($('input[name="child_num"]').val());

        var total = (price * num) + (price * child_discount * child_num) - coupon_price;
		//alert(total);
        $('#price').html('&yen;' + total);
		$('#total_price').html(total);
    }

    function select_payment(payment_id, jiage) {
        var coupon_box = document.getElementById('coupon_box');
        if( payment_id == 1 && coupon_box != null ) {
            coupon_box.style.display = 'block';
            //显示微信支付按钮
            $('#submit_wx').show();
            $('#submit_che').hide();
        } else if( payment_id == 0 ) {
            if( coupon_box != null ) {
                coupon_box.style.display = 'none';
            }
            $('#submit_wx').hide();
            $('#submit_che').show();
            $('#coupon_sel').val('0:0');
            select_coupon('0:0');
        }
        $('#payment_id').val(payment_id);
        document.getElementById('payment_id_' + payment_id).checked = true;
        price = jiage;
        $('#total_price_div').show();
        calTotal();
    }

    function select_coupon(val) {
        var a = val.split(':');
        $('#coupon_id').val(a[0]);
        if( max_coupon_price > 0 ) {
            var coupon_price = a[1] > max_coupon_price ? max_coupon_price : a[1];
        } else {
            var coupon_price = a[1];
        }
        $('#coupon_price').val(coupon_price);
        calTotal();
    }

    function minus() {
        var num = $.trim($('input[name="num"]').val());

        if (num == '') {
            num = 0;
        }

        if (num > 0) {
            num = parseInt(num) - 1;
        }

        $('input[name="num"]').val(num);

        calTotal();
    }

    function plus() {
        var num = $.trim($('input[name="num"]').val());

        if (num == '') {
            num = 0;
        }

        if (num >= 0) {
            num = parseInt(num) + 1;
        }


        $('input[name="num"]').val(num);

        calTotal();
    }

    function child_minus() {
        var num = $.trim($('input[name="child_num"]').val());

        if (num == '') {
            num = 0;
        }

        if (num > 0) {
            num = parseInt(num) - 1;
        }

        $('input[name="child_num"]').val(num);

        calTotal();
    }

    function child_plus() {
        var max_yuding = parseInt("3");
        var num = $.trim($('input[name="child_num"]').val());

        if (num == '') {
            num = 0;
        }

        if( num == max_yuding ) {
            alert('车票数量超出可预订数量');
            return false;
        }

        num = parseInt(num) + 1;

        $('input[name="child_num"]').val(num);

        calTotal();
    }

</script>
<style>
    .a_link{
        color: #eb7c1a;
        font-weight: bold;
        text-decoration: underline;
        padding-left: 10px;
    }
</style>
<script>
    //是否分享到朋友圈
    var has_share = false;
    var price_type = "1";
    $(function(){
		plus();
		
        var is_lock = false;
        $('.submit').click(function(){
            //特价必须要分享到朋友圈才能提交
            var this_type = $(this).attr('data-type');
            if( price_type != '1' && has_share === false && this_type != 1 ) {
                /*var html = '<div id="mcover" onclick="close_share_tip()"><img src="/Public/home/images/guide1.png" /></div>';
                $('body').append(html);*/

                $('#tj_tip_text').html('<img src="/Public/home/images/pyq.png" width="30" style="float: left" />点击右上角分享到朋友圈，享受特价购票乘车！</a>');
                $('#tj_tip').show();

                return false;
            }

            $('#price_type').val(this_type);

           /* var t = confirm("您确定要预订车票吗？");
            if( t == false ) {
                return false;
            }*/

            if( is_lock == false ) {
                is_lock = true;
                if( $('#username').val() == '' ) {
                    is_lock = false;
                    alerts('姓名不能为空！');
                    return false;
                }
                var phone = $('#phone').val();
                if( phone == '' ) {
                    is_lock = false;
                    alerts('手机号码不能为空！');
                    return false;
                }
                if( $('#sns_code').val() == '' ) {
                    is_lock = false;
                    alerts('请输入验证码！');
                    return false;
                }
                var re = /^1\d{10}$/;
                if(re.test(phone) === false){
                    is_lock = false;
                    alerts('手机号码不合法！');
                    return false;
                }
                var payment_id = $('#payment_id').val();

                if( payment_id == '' ) {
                    is_lock = false;
                    alerts('请选择一种支付方式！');
                    return false;
                }
                lock();
                $('#form1').submit();
            }
        })
    })
</script>


<script>
    //默认微信支付
    select_payment(1,parseFloat($('#single_price').html()).toFixed(1));
</script>

</body></html>