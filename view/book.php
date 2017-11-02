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
	<?php
		require_once "logicController.php";
		$data = get_book_data();
		$jssdk = new JSSDK($appid, $secret);
		$signPackage = $jssdk->GetSignPackage();
	?>
</head>
<body>
<div id="loading" style="position:fixed; width:100%; top:40%; display:none;">
	<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/loading.gif" style="text-align:center;width:50px;height:50px;" alt="" >
</div>


<div class="header">
    <div class="back"><a href="javascript:history.go(-1)">&lt; 返回</a></div>
    <div class="header-title">车票预订</div>
</div>

<form id="form1" method="post">
    <ul class="ticker-order">
        <li>
            <span>线路：</span>
			<?php
				echo $_SESSION['from_city'];
			?>
             &nbsp;
            <img src="http://bus-1251514843.cosbj.myqcloud.com/bus/arrow_right_black.png" alt="" style="width:50px">
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
			<span>正价票单价：</span>
			<b class="price" style="font-size:22px;font-weight:500">¥
			<?php
				echo $data['price'];
			?>
			</b>
		</li>
		<li id="special_price_li" style="display:none">
			<span>特价票单价：</span>
			<b class="price" style="font-size:22px;font-weight:500">¥
			<?php
				echo $data['special_price'];
			?>
			</b>
		</li>
		<li id="no_info_li">
			<span>票数：</span>
			<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add_press.png" alt="" style="width:28px" onclick="minus()">
			<input name="num" id="num" readonly="readonly" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px;text-align:center;font-size:18px;vertical-align:middle" value="1" size="3" type="text">
			<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add.png" alt="" style="width:28px" onclick="plus()">
		</li>
		<li id="info_li">
			<span>乘车人：</span><img src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add.png" alt="" style="width:28px" onclick="plus_customer()">
			<br/>
			<div id="customer_list">
				刘贺 <span>370203199202085917</span>
				<img style="width:28px;float:right" src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add_press.png" alt="" onclick="minus()">
				<br/>
				刘贺 <span>370203199202085917</span>
				<img style="width:28px;float:right" src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add_press.png" alt="" onclick="minus()">
			</div>
		</li>

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
				$coupons = get_book_coupon_data();
				
				foreach($coupons as $coupon){
					echo '<tr>
                        <td class="price" style="padding:10px 0;font-weight:300">
                            ¥ '.$coupon['coupon_price'].' 代金券';
					if($coupon['is_special_available'] != 1){
						echo '(不可用于特价票)';
					}		
                    echo '</td>
                        <td class="text-right"><input name="coupon_ratio" user_coupon_id="'.$coupon['user_coupon_id'].'" coupon_price="'.$coupon['coupon_price'].'" is_special_available="'. $coupon['is_special_available'] . '" type="radio"></td>
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
                <input name="username" id="username" class="input" placeholder="姓名" value="" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
            </p>

            <p>
            <span>手机号：</span>
                <input name="phone" id="phone" class="input" placeholder="手机号码" value="" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
            </p>
        </li>
        <!--<li style="dislay:none">
			<table>
				<tbody><tr>
					<td><span>总价：</span></td>
					<td class="text-right price" style="font-size:22px" id="total_price">¥0</td>
				</tr>
			</tbody></table>
		</li>-->    
	</ul>
</form>


<div class="navigation-footer" style="border-top: 1px solid #ddd;background: #eee;position: fixed;bottom: 0;left: 0;width: 100%;padding-top: 5px;padding-bottom: 5px;">
    <div class="container">
<?php

	
	
	
	echo '<div style="width: 48%; float: left;">
			<div id="total_price" class="button" onclick="buy_ticket(0,'.$data['price'].')" style="background: #EB600A;"></div>
		</div>';
	
	echo '<div id = "special_total_price_block" style="width: 48%; float: right; display:none">
			<div id="special_total_price" class="button" onclick="buy_ticket(1,'.$data['special_price'].')" ></div>
		</div>';

?>
	<div style="clear: both;"></div>	
        
		
	 </div>
</div>

<!--<div class="navigation-footer" style="border-top: 1px solid #ddd;background: #eee;position: fixed;bottom: 0;left: 0;width: 100%;padding-top: 5px;padding-bottom: 5px;">
    <div class="container">
        <div class="button" onclick="buy_ticket()" data-type="1">提 交 订 单</div>
	</div>
</div>-->

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
        <p>恭喜获得特价票购买资格，马上提交订单订票吧</p>
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
	(function($) {
		$.extend({       
		urlGet:function()
		{
			var aQuery = window.location.href.split("?");  //取得Get参数
			var aGET = new Array();
			if(aQuery.length > 1)
			{
				var aBuf = aQuery[1].split("&");
				for(var i=0, iLoop = aBuf.length; i<iLoop; i++)
				{
					var aTmp = aBuf[i].split("=");  //分离key与Value
					aGET[aTmp[0]] = aTmp[1];
				}
			 }
			 return aGET;
		 }
		})
	})(jQuery);
	
			
	function minus_customer(idx){
		var len = (idx >= 0)?1:0;
		customer_name_array.splice(idx, len);
		customer_id_card_array.splice(idx, len);
		var html = '';
		
		for(var i=0;i<customer_name_array.length;i++){
			html = html + '<div>' + customer_name_array[i] + '<span> ' + customer_id_card_array[i] + '</span><img style="width:28px;float:right" src="http://bus-1251514843.cosbj.myqcloud.com/bus/image_btn_add_press.png" alt="" onclick="minus_customer(' + i + ')"><br/><div>';			
		}
		
		$('#customer_list').html(html);
		//alert($('#customer_list').html());
		
	}
	
	function plus_customer(){
		var names_str = "";
		var id_cards_str = "";
		for(var i=0;i<customer_name_array.length;i++){
			names_str = names_str + ((i==0)?"":">") + customer_name_array[i];
			id_cards_str = id_cards_str + ((i==0)?"":">") + customer_id_card_array[i];
		}
		window.location.href='customer.php?customer_names='+names_str+'&customer_id_cards='+id_cards_str;
	}
	
	 //调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			jsApiParameters ,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == "get_brand_wcpay_request:ok" ) {
					var post_data = {};	
					post_data['action'] = 'book_ticket_success';
					$.ajax({
						type        : 'post',
						url         : SERVER_URL,
						async		: false,
						data        : { 'request'   : JSON.stringify(post_data) },
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
				}else{
					cancel_ticket();
				}
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}
	
	//在未支付的情况下取消预定
	function cancel_ticket(){
		var post_data = {};	
		post_data['action'] = 'book_ticket_cancel';
		$.ajax({
			type        : 'post',
			url         : SERVER_URL,
			async		: false,
			data        : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				if(data['suc'] == 1){
					//alert(data['msg']);
				}else{
					alert(data['msg']);
				}
			},
			error		:function(err){
				alert("err:"+JSON.stringify(err));
			}
		})
	}
	
	var is_customer_info_needed = 0;
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
	var user_coupon_id = -1;
	var coupon_price = 0;
	var is_special_available = -1;//特价票是否可用此代金券
	var total_price = 0;
	var special_total_price = 0;
	var num = 0;
	var is_shared = false;
	var is_halt = false;
	var jsApiParameters;
	var GET = $.urlGet(); //获取URL的Get参数
	var customer_name_array = new Array();
	var customer_id_card_array = new Array();
	
	if(is_customer_info_needed == 1){
		$('#no_info_li').hide();
		$('#info_li').show();
	}else{
		$('#no_info_li').show();
		$('#info_li').hide();
	}
	
	if(typeof(GET['customer_names']) != "undefined" && typeof(GET['customer_id_cards']) != "undefined"){
		customer_name_array = decodeURI(GET['customer_names']).trim().split(">"); 
		customer_id_card_array = decodeURI(GET['customer_id_cards']).trim().split(">");
	}
	minus_customer(-1);
	
	var apiList = [
		  // 所有要调用的 API 都要加到这个列表中
		  'onMenuShareTimeline'
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
				if(apiCheckList['onMenuShareTimeline']){
					wx.onMenuShareTimeline({
						title: '疯抢！<?php echo $_SESSION["from_city"];?>-<?php echo $_SESSION["to_city"];?> 大巴特价票低至'+data['special_price']+'元！不知道的亏大了！', // 分享标题
						link: 'http://helibus.cn/bus/view/index.php', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
						imgUrl: 'http://bus-1251514843.cosbj.myqcloud.com/bus/firm-logo.jpg', // 分享图标
						success: function(){
							// 用户确认分享后执行的回调函数
							is_shared = true;
							$('#share_success').show();
							setTimeout(function(){
								//$('#mcover').remove();
								$('#share_success').hide();
							},2000);
						},
						cancel: function () { 
							// 用户取消分享后执行的回调函数
							//alert("请点击右上角分享本页面到朋友圈获得特价票特权！");
						}
					});
				}
			}
		});
	  });
	 
	
</script>
<script type="text/javascript">
	
	function calTotal() {
		if(is_customer_info_needed == 1){
			num = customer_name_array.length;
		}else{
			num = $.trim($('input[name="num"]').val());
		}
		
		total_price = ((data['price'] * num) - coupon_price);
		if(total_price < 0){
			total_price = 0;
		}
		total_price = total_price.toFixed(2);
		
		special_total_price = (data['special_price'] * num);
		if(is_special_available == 1){
			special_total_price -= coupon_price;
		}
		if(special_total_price < 0){
			special_total_price = 0;
		}
		special_total_price = special_total_price.toFixed(2);
		
		if(data['special_ticket_num'] > 0){
			if(data['available_num'] > 0){
				//alert('正价&yen;' + total_price + '购票');
				$('#total_price').html('正价&yen;' + total_price + '购票');
			}else{
				$('#total_price').html('正价&yen;' + total_price + '(已售罄)');
			}
			if(data['available_special_num'] > 0){
				$('#special_total_price').html('特价&yen;' + special_total_price + '购票');
			}else{
				$('#special_total_price').html('特价&yen;' + special_total_price + '(已售罄)');
			}
			$('#special_total_price_block').show();
			$('#special_price_li').show();
		}else{
			if(data['available_num'] > 0){
				$('#total_price').html('共&yen;' + total_price + '购票');
			}else{
				$('#total_price').html('共&yen;' + total_price + '(已售罄)');
			}
		}
		
	}
	function buy_ticket(is_special_ticket, var_price){
		if(is_halt == true){
			return;
		}
		
		calTotal();
		if(num == 0){
			alert("请购买至少一张车票");
			return;
		}
		
		//如果已售罄或者余票不足，不可购买
		if(is_special_ticket==1 && num > data['available_special_num']){
			alert("余票不足，特价票剩余"+data['available_special_num']+"张");
			return;
		}
		if(is_special_ticket==0 && num > data['available_num']){
			alert("余票不足，正价票剩余"+data['available_num']+"张");
			return;
		}
		
		//如果特价票且要分享朋友圈且未分享过朋友圈，则必须先分享，分享之后存到session
		if(is_special_ticket == 1 && is_shared == false){
			alert("请点击右上角分享到朋友圈，即可获得特价票特权！");
			return;
		}
		
		if($.trim($('#is_expire').html()) == '1'){
			if(!window.confirm('此班次的发车时间已过期，确认购买吗？')){
				 return;
			}
		}
		var post_data = {};	
		post_data['action'] = 'book_ticket';
		post_data['is_special_ticket'] = is_special_ticket;
		post_data['ticket_num'] = num;
		
		post_data['is_customer_info_needed'] = is_customer_info_needed;
		//available only is_customer_info_needed = 1,现在is_customer_info_needed，customer_name_array，customer_id_card_array都没有在后台使用
		post_data['customer_name_array'] = is_customer_info_needed;
		post_data['customer_id_card_array'] = customer_id_card_array;
		
		if((is_special_ticket == 0 && user_coupon_id != -1) || (is_special_ticket == 1 && is_special_available == 1 && user_coupon_id != -1)){
			post_data['user_coupon_id'] = user_coupon_id;
		}
		
		$.ajax({
			type        : 'post',
			url         : SERVER_URL,
			async		: false,
			data        : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			beforeSend	: function(){
				$('#loading').show();
				is_halt = true;
			},
			success     : function(data) {
				if(data['suc'] == 1){
					//window.location.href=data['url'];
					jsApiParameters = JSON.parse(data['msg']);
					callpay();
					
				}else{
					alert(data['msg']);
				}
				$('#loading').hide();
				is_halt = false;
			},
			error		:function(err){
				alert(JSON.stringify(err));
				$('#loading').hide();
				is_halt = false;
			}
		})
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

		if(num >= 3){
			alert("单个订单最多购买3张车票，更多车票请多次购买");
			return;
		}
		
		if (num >= 0) {
			num = parseInt(num) + 1;
		}


		$('input[name="num"]').val(num);
		calTotal();
	}
</script>
<script type="text/javascript">

var data = JSON.parse('<?php echo json_encode($data); ?>');
data['available_num'] = parseInt(data['available_num']);
data['available_special_num'] = parseInt(data['available_special_num']);
data['special_ticket_num'] = parseInt(data['special_ticket_num']);
calTotal();


$('input[name="coupon_ratio"]').click(function(){  
	
	var currentState = $(this).attr('checked');
	if(currentState == null || currentState == false){
		$(this).attr('checked', true);
		user_coupon_id = parseInt($(this).attr('user_coupon_id'));
		coupon_price = parseFloat($(this).attr('coupon_price'));
		is_special_available = parseInt($(this).attr('is_special_available'));
	}else{
		$(this).attr('checked', false);
		user_coupon_id = -1;
		coupon_price = 0;
		is_special_available = -1;
	}
	
	calTotal();
});  
</script>


</body>
</html>