<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>调查问卷</title>
	<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/func.js"></script>
    <link rel="stylesheet" href="http://bus-1251514843.cosbj.myqcloud.com/css/weui.css"/>
    <link rel="stylesheet" href="http://bus-1251514843.cosbj.myqcloud.com/css/survey_example.css"/>
</head>
<body ontouchstart>    
<div class="container" id="container"></div>

<script type="text/html" id="tpl_home">
<div class="page">
    <div class="page__hd">
        <p class="page__desc">作答领取价值10元代金券！快来邀请朋友们参与吧！</p>
    </div>
    
    <div class="page__bd">
       <?php
		
			include 'logicController.php';
			$ps = array();
			
			$p = array();
			$p['title'] = '1）上车时间:';
			$p['options'] = array();
			$p['options'][] = '08:00';
			$p['options'][] = '09:00';
			$p['options'][] = '其他';
			
			$ps[] = $p;
			
			echo '<div style="display:none;" id="ps_len">'.count($ps).'</div>';
			
			for($p_i = 0; $p_i<count($ps); $p_i++){
				echo '
					<div class="weui-cells__title" id="p_title'.$p_i.'">'.$ps[$p_i]["title"].'</div>
					<div class="weui-cells weui-cells_checkbox">
						
					';
				for($o_i = 0; $o_i<count($ps[$p_i]['options']); $o_i++){
					echo '
						<label class="weui-cell weui-check__label" for="s'.$p_i.$o_i.'">
							<div class="weui-cell__hd">
								<input type="checkbox" onchange="changeState(this, \''.$p_i.'>'.$ps[$p_i]['options'][$o_i].'\');" class="weui-check" name="checkbox'.$p_i.'" id="s'.$p_i.$o_i.'"/>
								<i class="weui-icon-checked"></i>
							</div>
							<div class="weui-cell__bd">
								<p name="option_val'.$p_i.$o_i.'">'.$ps[$p_i]['options'][$o_i].'</p>
							</div>
					';
					
					echo '</label>';
				}
				if($ps[$p_i]['options'][count($ps[$p_i]['options'])-1] == '其他'){
					echo '
					<label style="display:none;" id="other'.$p_i.'" class="weui-cell weui-check__label">
						<!--<div class="weui-cell__hd">
							<input type="checkbox" class="weui-check"/>
							<i class="weui-icon-checked"></i>
						</div>-->
						 <div class="weui-cell__bd">
							<input class="weui-input" type="text" name="input'.$p_i.'" placeholder="请输入其他情况"/>
						</div>
					</label>
					';
				}
			}
			
		?>
       
     
		</div>
		<div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" onclick="postData();" id="showTooltips">确定</a>
        </div>
        
    </div>
</div>
</script>
<script>
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;

	function changeState(checkbox, param){
		var p_i = param.split(">",-1)[0].trim();
		var val = param.split(">",-1)[1].trim();
		
		if(val == '其他' && checkbox.checked){
			$('#other'+p_i).show();
		}else if(val == '其他'){
			$('#other'+p_i).hide();
		}
	}
	
	function postData(){
		var ps_len = parseInt($('#ps_len').html().trim());//题的个数
		var ps = new Array();
		
		for(var p_i=0; p_i<ps_len; p_i++){
			var checkbox1s = $('input[name="checkbox'+p_i+'"]');
			if(checkbox1s[checkbox1s.length-1].checked){
				var othertext = $('input[name="input'+p_i+'"]');
				if(othertext.val().trim() == ''){
					alert('请具体描述第'+(p_i+1)+'题的其他情况');
					return;
				}
			}
			var p = {};
			var text = '';
			for(var o_i=0;o_i<checkbox1s.length;o_i++){
				if(checkbox1s[o_i].checked){
					var option_val = $('p[name="option_val'+p_i+o_i+'"]').html().trim();
					if(option_val == '其他'){
						option_val = $('input[name="input'+p_i+'"]').val().trim();
					}
					text = text + option_val + '>';
				}
			}
			
			if(text == ''){
				alert('请回答第'+(p_i+1)+'个问题');
				return;
			}
			p['problem_value']=text;
			p['problem_title']=$('#p_title'+p_i).html().trim();
			
			ps.push(p);
		}
		
		var post_data = {};	
		post_data['action'] = 'post_survey';
		post_data['survey_sheet_name'] = '测试问卷';
		post_data['ps'] = ps;
		
		$.ajax({
			type        : 'post',
			url         : SERVER_URL,
			async		: false,
			data        : { 'request'   : JSON.stringify(post_data) },
			dataType    : 'json',
			success     : function(data) {
				alert(data['msg']);
			},
			error		:function(err){
				alert("err:"+JSON.stringify(err));
			}
		})
	}
</script>
<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/zepto.min.js"></script>
<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/weui.min.js"></script>
<script type="text/javascript" src="http://bus-1251514843.cosbj.myqcloud.com/js/survey_example.js"></script>
</body>
</html>
