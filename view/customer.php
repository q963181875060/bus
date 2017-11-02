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
		$data = get_customers()[0];
	?>
</head>
<body>
<div id="loading" style="position:fixed; width:100%; top:40%; display:none;">
	<img src="http://bus-1251514843.cosbj.myqcloud.com/bus/loading.gif" style="text-align:center;width:50px;height:50px;" alt="" >
</div>


<div class="header">
    <div class="back"><a href="javascript:history.go(-1)">&lt; 返回</a></div>
    <div class="header-title">乘车人列表</div>
</div>

<form id="form1" method="post">
    <ul class="ticker-order">
	<?php
		
		
		if(trim($data['names']) == ''){
			$names = array();
			$id_cards = array();
		}else{
			$names = split('>', $data['names']);
			$id_cards = split('>', $data['id_cards']);
		}
		$num = count($names);
		for($i=0;$i<$num;$i++){
			
			echo '<li>
					<input id="selected_customer_' .$i .'" name="selected_customer" type="checkbox" value="' .$i .'"/>
					<span onclick="choose_customer('.$i.');"><span style="color:#000;margin-left:10px;font-size:16px;">'.$names[$i].'</span>'.$id_cards[$i].'</span>
					<a style="float:right;color:#2182c0;font-size:14px;" onclick="pop_customer_window('.$i.');">修改</a>
					
				</li>';	
			
			
		}
	?>
		
				<li style="text-align:center">
					<a style="color:#2182c0" onclick="pop_customer_window(-1);">添加新乘客</a>
				</li>
	</ul>
</form>


<div class="navigation-footer" style="border-top: 1px solid #ddd;background: #eee;position: fixed;bottom: 0;left: 0;width: 100%;padding-top: 5px;padding-bottom: 5px;">
    <div class="container">
		<div id="total_price" class="button" onclick="confirm_customer();" style="background: #EB600A;">确定</div>
	</div>
</div>

<div class="notice-box" id="edit_customer">
    <div>
        <p>
			<span>乘客姓名：</span>
			<input id="name" class="input" placeholder="" value="" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
		</p>

		<p>
			<span>身份证号：</span>
			<input id="id_card" class="input" placeholder="" value="" style="outline:none;border:1px solid #ccc;border-radius: 3px;background:#fff;height:32px; padding-left:12px;font-size:18px;vertical-align:middle" type="text">
		</p>
    </div>
    <p>
		<span onclick="change_customer();">确定</span>
		<span style="margin-left:80px" onclick="$('#edit_customer').hide()">取消</span>
	</p>
</div>

<script type="text/javascript">
	function pop_customer_window(idx){
		if(cur_names.length >= 8){
			alert("乘车人数量已达上限");
			return;
		}
		customer_idx = idx;
		if(customer_idx >= 0){
			$('#name').val(cur_names[customer_idx]);
			$('#id_card').val(cur_id_cards[customer_idx]);
		}else{
			$('#name').val("");
			$('#id_card').val("");
		}
		$('#edit_customer').show();
	}
	
	function change_customer(){
		if(is_halt == true) return;
		
		
		var tmp_name = $('#name').val().trim();
		var tmp_id_card = $('#id_card').val().trim();
		if(tmp_name == "" || tmp_id_card == ""){
			alert("姓名和身份证号不能为空");
			return;
		}
		if(tmp_name.indexOf(">") >= 0 || tmp_id_card.indexOf(">") >= 0){
			alert("姓名和身份证号不能包含>符号");
			return;
		}
		
		var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
		if (reg.test(tmp_id_card) === false) {
			alert("身份证号格式有误");
			return false;
		}
		
		//all test OK
		is_halt = true;
		var names = new Array();
		var id_cards = new Array();
		if(names_str_pre.trim() != ""){
			names = names_str_pre.split(">", -1);
			id_cards = id_cards_str_pre.split(">", -1);
		}
		
		if(customer_idx == -1){
			names.push(tmp_name);
			id_cards.push(tmp_id_card);
		}else{
			names[customer_idx] = tmp_name;
			id_cards[customer_idx] = tmp_id_card;
		}
		
		names_str = "";
		id_cards_str = "";
		for(var i=0;i<names.length;i++){
			names_str = names_str + ((i==0)?"":">") + names[i];
			id_cards_str = id_cards_str + ((i==0)?"":">") + id_cards[i];
		}
		
		var post_data = {};	
		post_data['action'] = 'change_customer';
		post_data['names'] = names_str;
		post_data['id_cards'] = id_cards_str;
		
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
				is_halt = false;
			},
			error		:function(err){
				alert(JSON.stringify(err));
				is_halt = false;
			}
		})
	}
	
	function choose_customer(idx){
		var currentState = $('#selected_customer_'+idx).prop('checked');
		if(currentState == null || typeof(currentState) == "undefined" || currentState == false){
			$('#selected_customer_'+idx).prop('checked', "checked")
		}else{
			$('#selected_customer_'+idx).removeAttr('checked');
		}
	}
	
	
	function confirm_customer(){
		names_str = "";
		id_cards_str = "";
		var num = 0;
		$.each($('input[name="selected_customer"]:checkbox'),function(){
			if(this.checked){
				if(names_str == ""){
					names_str = cur_names[parseInt($(this).val())];
					id_cards_str = cur_id_cards[parseInt($(this).val())];
				}else{
					names_str = names_str + ">" + cur_names[parseInt($(this).val())];
					id_cards_str = id_cards_str + ">" + cur_id_cards[parseInt($(this).val())];
				}
				num++;
			}
		});
		if(num > 3){
			alert("一次订单最多购买3张车票");
			return;
		}
		window.location.href="book.php?customer_names="+names_str+"&customer_id_cards="+id_cards_str;
	}
	
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

	
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
	var customer_idx = -2;
	var names_str_pre = '<?php echo $data['names']; ?>';
	var id_cards_str_pre = '<?php echo $data['id_cards']; ?>';
	var cur_names = new Array();
	var cur_id_cards = new Array();
	var is_halt = false;
	
	if(names_str_pre.trim() != ""){
		cur_names = names_str_pre.trim().split(">", -1);
		cur_id_cards = id_cards_str_pre.trim().split(">", -1);
	}
	var GET = $.urlGet(); //获取URL的Get参数
	var req_customer_name_array = new Array();
	var req_customer_id_card_array = new Array();
	if(typeof(GET['customer_names']) != "undefined" && typeof(GET['customer_id_cards']) != "undefined"){
		req_customer_name_array = decodeURI(GET['customer_names']).trim().split(">"); 
		req_customer_id_card_array = decodeURI(GET['customer_id_cards']).trim().split(">");
	}
	for(var i=0;i<cur_names.length;i++){
		for(var j=0;j<req_customer_name_array.length;j++){
			if(cur_names[i] == req_customer_name_array[j] && cur_id_cards[i] == req_customer_id_card_array[j]){
				choose_customer(i);
			}
		}
	}


</script>


</body>
</html>