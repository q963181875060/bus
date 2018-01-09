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
</head>
<body>
<div id="lock" style="display: none">
    <div id="shadow_loading"></div><div id="shadow_bg"></div>
</div>


<div class="header">

    <div class="back"><a href="javascript:void(0)" onclick="window.history.back()">&lt; 返回</a></div>

    <div class="header-title">
		<?php
			//如果没按正常流程到此页面，则跳转到首页
			if(!isset($_SESSION['city_direction'])){\
				error_log("no city direction");
				Header("HTTP/1.1 303 See Other"); 
				Header("Location: index.php"); 
				exit; 
			}
				
			if($_SESSION['city_direction']==0){
				echo '选择出发城市';
			}else{
				echo '选择到达城市';
			}
		?> 	
	</div>
</div>

<!--<div class="cities-title">当前城市</div>
<ul class="cities" id="current_city"><li><a href="http://guangyunbus.com/index.php/Index/index/from_city/1">深圳</a></li></ul>

<div class="clear" style="height:10px"></div>-->

<div class="cities-title">已开通城市</div>

<ul class="cities" id="city_list">
	<?php
		include 'logicController.php';
		$data = get_select_city_data();
		foreach($data as $key=>$value){
			echo '<li><a onclick="select_city(\''.$key.'\')">'.$key.'</a></li>';
		}
	?> 
</ul>

<div class="clear" style="height:10px"></div>

<script type="text/javascript">
	var SERVER_URL = 'clientController.php';
	var AJAX_TIMEOUT = 2000;
    var latitude = '';
    var longitude = '';

	function select_city(selected_city){
		var post_data = {};	
		post_data['action'] = 'goto_index';
		post_data['selected_city'] = $.trim(selected_city);
		
		$.ajax({
            type        : 'post',
            url         : SERVER_URL,
			async		: false,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
                window.location.href=data['url'];
            }
        })
	}
	
    function locationSuccess(position) {
        var coords = position.coords;

        latitude = coords.latitude; //纬度

        longitude = coords.longitude; //经度
    }

    function locationError(error) {
        /*
         switch(error.code)
         {
         case 0:
         alert("There was an error while retrieving your location: " + error.message);
         break;
         case 1:

         break;
         case 2:
         alert("The browser was unable to determine your location: " + error.message);
         break;
         case 3:
         alert("The browser timed out before retrieving the location.");
         break;
         }
         */
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(locationSuccess, locationError, {
                enableHighAcuracy: true,
                maximumAge: 3000
            });
        }
    }

    //getLocation();

	
    /*function getData() {
		var post_data = {};
		post_data['action'] = 'select_city_get_data';
        $.ajax({
            type        : 'post',
            url         : SERVER_URL,
            data        : { 'request'   : JSON.stringify(post_data) },
            dataType    : 'json',
            success     : function(data) {
          
				
				city_direction = data['city_direction'];
				from_city = data['from_city'];
				
				var html = '';
				alert("suc:"+data['city_direction']+"  "+data['from_city']);
				for(var i=0;i<data['cities'].length;i++){
					html = html + '<li><a href="javascript:void(0)" onclick="select_city(\'' + data['cities'][i] + '\')">' + data['cities'][i] + '</a></li>';
				}
				
				$('#city_list').html(html);
            }
        })
    }

    $(document).ready(function(){
        getData();
    });*/
</script>


<?php
include 'nav_bar.php';
?>

</body></html>