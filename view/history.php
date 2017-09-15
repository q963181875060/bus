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
foreach($data as $book){
	if($book['state'] == '正常'){
		echo '<li class="li_normal">';
	}else{
		echo '<li class="li_grey">';
	}
	
	echo '
			<table>
				<thead>
				<tr>
					<td colspan="3" class="from_to">
						'.$book['from_city'].'
						<img src="pic/arrow.png" style="width:35px;vertical-align:middle" alt="">
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
					<td>乘车站点</td>
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
					<td><span class="price" style="font-size:18px">¥
						'.$book['price'].'
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

	var tmp_req_url = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
    var book_id = 0;
    function cancel_order(id) {
        book_id = id;
        $('#notice-box').show();
    }

    function cancel_order_do(){
        if( !(book_id > 0) ) {
            alert('参数错误');
            return false;
        }
        var post_data = {};	
		post_data['action'] = 'cancel_book';
		post_data['book_id'] = book_id;
		
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
</script></body></html>