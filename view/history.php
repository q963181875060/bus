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
foreach($data as $book){
	if($book['state'] == '正常'){
		echo '<li class="li_normal">';
	}else if($book['state'] == '待支付' || $book['state'] == '已取消'){
		continue;
	}else{//已退票，已过期, 待验票
		echo '<li class="li_grey">';
	}
	
	echo '
			<table>
				<thead>
				<tr>
					<td colspan="3" class="from_to">
						'.$book['from_city'].'
						<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/arrow.png" style="width:35px;vertical-align:middle" alt="">
						'.$book['to_city'].'
					</td>
					<td>
						<span class="status_name">
							'.$book['state'].'
						</span>
					</td>
				</tr>
				</thead>

				<tbody>
					<tr>
						<td>订单编号</td>
						<td>
							'.$book['book_id'].'
						</td>
						<td>验票码</td>
						<td>
							'.$book['verify_code'].'
						</td>
					</tr>        <tr>
					<td style="width:25%">乘车站点</td>
					<td colspan="3">
						'.$book['from_stop'].'
					</td>
				</tr>
				<tr>
					<td>乘车日期</td>
					<td colspan="3">
						'.$book['start_date'].' '.$book['from_time'].'
					</td>
				</tr>
				<tr>
					<td>领队电话 </td>
					<td>
						'.$book['contact_mobile'].'
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
						<td>票数</td>
						<td>
							'.$book['ticket_num'].'
						</td>
						<td></td>
						<td></td>
				</tr>        
				<tr>
					<td>单价</td>
					<td>
						<span class="price" style="font-size:18px">¥
							'.$book['price'].'
						</span>
					</td>';
		if($book['coupon_price'] != '' && $book['coupon_price'] != 0){
			echo '<td>使用代金券</td>
					<td>
						<span class="price" style="font-size:18px">¥
							'.$book['coupon_price'].'
						</span>
					</td>';
		}else{
			echo '<td></td>
				  <td></td>';
		}
					
		echo	'</tr>

				<tr><td colspan="4">
					<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/gantan.png" style="width:20px;vertical-align:middle" alt=""> <span style="color:#ff5500">请提前15分钟等候上车，出发前1小时内不可退</span></td>
				</tr>        </tbody>
			</table>
			';
	if($book['state'] == '正常'){
		echo '
					<div class="order-buttons">
						<!--<a href="http://guangyunbus.com/index.php/User/continue_pay/id/1589331">继续支付</a>-->
						<a class="cancel_order" href="javascript:cancel_order('.$book['book_id'].')">取消车票</a></div>        <div class="clear">
					</div>';
	}
			
	echo '</li>';
	
}
?>

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

<?php
include 'nav_bar.php';
?>


<script>
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
    var book_id = 0;
	var is_halt = false;
	
    function cancel_order(id) {
        book_id = id;
        $('#notice-box').show();
    }

    function cancel_order_do(){
		if(is_halt == true){
			return;
		}
		is_halt = true;
        if( !(book_id > 0) ) {
            alert('参数错误');
            return false;
        }
        var post_data = {};	
		post_data['action'] = 'cancel_book';
		post_data['book_id'] = book_id;
		
		$.ajax({
            type        : 'post',
            url         : SERVER_URL,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
				if(data['suc'] == 1){
					alert('退票成功，已退款到您的账户，请注意查收');
					window.location.href=data['url'];
				}else{
					alert(data['msg']);
					is_halt = false;
				}
            }
        })
    }
</script></body></html>