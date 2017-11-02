<?php
require_once('common.php');
require_once('jssdk.php');

//判断openid是否存在
date_default_timezone_set('Asia/Shanghai');	

if(!isset($_SESSION['user_id']) || trim($_SESSION['user_id']) == ''){
	if(isset($_GET['code'])){
		$code = $_GET['code'];
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$html = file_get_contents($url);
		$req = json_decode($html,1);
		//print_r($req);
		if(isset($req['openid'])){
			//echo "微信登录成功";//.$req['openid'];
			$_SESSION['user_id']=trim($req['openid']);
		}else{
			echo "微信登录失败";
		}
	}else{
		echo "微信登录失败";
	}
}else{
	//echo "微信登录成功".$_SESSION['user_id'];
}


//返回选择的城市列表
function get_select_city_data(){
	try {
		global $DBSTR, $user, $pass;
		$exe_params = array();
		
		$dbh = new PDO ($DBSTR,$user,$pass); 
		if($_SESSION['city_direction'] == 0){
			$sql = "select from_city as city, available_dates from route_table order by from_city asc";
		}else{
			$sql = "select to_city as city, available_dates from route_table where from_city=:from_city order by to_city asc";
			$exe_params[':from_city']=trim($_SESSION['from_city']);
		}
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$data = $sth->fetchAll(PDO::FETCH_ASSOC);
		$result = array();
		
		//只有今后有班次安排的城市才会出现在城市列表
		foreach($data as $unit){
			$dates = split('>', $unit['available_dates']);
			if(strtotime($dates[0]) >= strtotime(date("Y-m-d")) || strtotime($dates[count($dates)-1]) >= strtotime(date("Y-m-d"))){
				$result[$unit['city']] = '';
			}
		}
		
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

//返回两城市间的停靠点
function get_select_stop_data(){
	try {
		global $DBSTR, $user, $pass;
		$exe_params = array();
		
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sql = "select from_stops, to_stops from route_table where from_city=:from_city and to_city=:to_city and available_dates like :available_dates";
		$exe_params[':from_city']=trim($_SESSION['from_city']);
		$exe_params[':to_city']=trim($_SESSION['to_city']);
		$exe_params[':available_dates']='%'.trim($_SESSION['start_date']).'%';
		//error_log($_SESSION['start_date']);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
	//return array('from_stops'=>array('始发站1','始发站2'), 'to_stops'=>array('终点站1','终点站2'));
}

//返回两停靠点的某天的班车安排
function get_select_time_data(){
	
	try {
		global $DBSTR, $user, $pass;
		$exe_params = array();
		
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sql = "select tab1.route_id, from_stops, from_times, special_price, special_must_share, price, special_ticket_num, tab1.ticket_num, is_special_ticket, tab2.booked_num from
				(
					select * from route_table 
					where 
						from_city=:from_city and to_city=:to_city and available_dates like :start_date and 
						from_stops like :from_stop and to_stops like :to_stop
				)tab1
				left join
				(
					select route_id, is_special_ticket, sum(ticket_num) booked_num from book_table
					where 
						from_city=:from_city1 and to_city=:to_city1 and start_date=:start_date1 and state in ('正常', '待支付', '待验票', '已过期', '已验票')
					group by 
						route_id, is_special_ticket
				)tab2 on (tab1.route_id = tab2.route_id)
				";
		$exe_params[':from_city']=trim($_SESSION['from_city']);
		$exe_params[':to_city']=trim($_SESSION['to_city']);
		$exe_params[':start_date']='%'.trim($_SESSION['lookup_date']).'%';
		$exe_params[':from_stop']='%'.trim($_SESSION['from_stop']).'%';
		$exe_params[':to_stop']='%'.trim($_SESSION['to_stop']).'%';
		$exe_params[':from_city1']=trim($_SESSION['from_city']);
		$exe_params[':to_city1']=trim($_SESSION['to_city']);
		$exe_params[':start_date1']=trim($_SESSION['lookup_date']);
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$tmp_res = $sth->fetchAll(PDO::FETCH_ASSOC);
		$routes = array();
		foreach($tmp_res as $tmp){
			if(!isset($routes[$tmp['route_id']])){
				$routes[$tmp['route_id']] = array();
			}
			$from_stops = split('>',$tmp['from_stops']);
			$idx = 0;
			for(; $idx<count($from_stops); $idx++){
				if(trim($from_stops[$idx]) == trim($_SESSION['from_stop'])){
					break;
				}
			}
			$routes[$tmp['route_id']]['from_time'] = split('>',$tmp['from_times'])[$idx];
			
			if(!isset($routes[$tmp['route_id']]['available_special_num'])){
				$routes[$tmp['route_id']]['available_special_num'] = $tmp['special_ticket_num'];
			}
			if(!isset($routes[$tmp['route_id']]['available_num'])){
				$routes[$tmp['route_id']]['available_num'] = $tmp['ticket_num'];
			}
			if($tmp['is_special_ticket'] == 1){
				$routes[$tmp['route_id']]['available_special_num'] -= $tmp['booked_num'];
			}
			$routes[$tmp['route_id']]['available_num'] -= $tmp['booked_num'];
			$routes[$tmp['route_id']]['special_price'] = $tmp['special_price'];
			$routes[$tmp['route_id']]['price'] = $tmp['price'];
			$routes[$tmp['route_id']]['special_must_share'] = $tmp['special_must_share'];
			$routes[$tmp['route_id']]['special_ticket_num'] = $tmp['special_ticket_num'];
		}
		foreach($routes as $key=>$route){
			if($route['available_special_num'] > $route['available_num']){
				$routes[$key]['available_special_num'] = $routes[$key]['available_num'];
			}
		}
		
		$result = $routes;
	
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

//返回待支付车票的信息
function get_book_data(){	
	try {
		global $DBSTR, $user, $pass;
		$exe_params = array();
		
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sql = "select tab1.route_id, tab1.special_price, tab1.price, tab1.special_ticket_num, tab1.ticket_num, tab1.special_must_share, is_special_ticket, tab2.booked_num from
				(
					select * from route_table 
					where 
						route_id=:route_id and available_dates like :start_date
				)tab1
				left join
				(
					select route_id, is_special_ticket, sum(ticket_num) booked_num from book_table
					where 
						route_id=:route_id1 and start_date=:start_date1 and state in ('正常', '待支付', '待验票', '已过期', '已验票')
					group by 
						is_special_ticket
				)tab2 on (tab1.route_id = tab2.route_id)
				";
		$exe_params[':route_id']=trim($_SESSION['route_id']);
		$exe_params[':start_date']='%'.trim($_SESSION['lookup_date']).'%';
		$exe_params[':route_id1']=$exe_params[':route_id'];
		$exe_params[':start_date1']=trim($_SESSION['lookup_date']);
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$tmp_res = $sth->fetchAll(PDO::FETCH_ASSOC);
		$routes = array();
		foreach($tmp_res as $tmp){
			if(!isset($routes['available_special_num'])){
				$routes['available_special_num'] = $tmp['special_ticket_num'];
			}
			if(!isset($routes['available_num'])){
				$routes['available_num'] = $tmp['ticket_num'];
			}
			if($tmp['is_special_ticket'] == 1){
				$routes['available_special_num'] -= $tmp['booked_num'];
			}
			$routes['available_num'] -= $tmp['booked_num'];
			$routes['special_price'] = $tmp['special_price'];
			$routes['price'] = $tmp['price'];
			$routes['special_must_share'] = $tmp['special_must_share'];
			$routes['special_ticket_num'] = $tmp['special_ticket_num'];
		}
		
		if($routes['available_special_num'] > $routes['available_num']){
			$routes['available_special_num'] = $routes['available_num'];
		}
		
		$result = $routes;
	
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

/*function getMillisecond() { 
	list($s1, $s2) = explode(' ', microtime()); 
	return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
} */

function get_history_data(){
	
	try {
		
		//1) 过期超过一小时，则设置为过期车票
		//$_SESSION['user_id'] = 1;
		
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		
		
		/*
		(str_to_date(start_date,'%Y-%m-%d') < str_to_date(:start_date,'%Y-%m-%d') or 
					(str_to_date(start_date,'%Y-%m-%d') = str_to_date(:start_date1,'%Y-%m-%d')
						and time_to_sec(from_time) <= time_to_sec(:from_time)))*/
		$sql = "update book_table set state='已过期' 
					where user_id=:user_id and state='正常' and 
					UNIX_TIMESTAMP(concat(start_date, ' ', from_time)) <= UNIX_TIMESTAMP(:current_time)";
		$exe_params = array();
		$exe_params[':user_id']=$_SESSION['user_id'];
		$exe_params[':current_time']=date('Y-m-d H:i',strtotime('-1 hours'));
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		//error_log('update state number ='.$sth->rowCount());
		
		//2) 如果有1小时内过期的，则设置为待验票状态
		$sql = "update book_table set state='待验票' 
					where user_id=:user_id and state='正常' and 
					UNIX_TIMESTAMP(concat(start_date, ' ', from_time)) <= UNIX_TIMESTAMP(:current_time)";
		$exe_params = array();
		$exe_params[':user_id']=$_SESSION['user_id'];
		$exe_params[':current_time']=date('Y-m-d H:i',strtotime('+1 hours'));
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		//返回订单
		$exe_params = array();
		$sql = "select book_id, verify_code, from_city, to_city, from_stop, to_stop, start_date, from_time, contact_mobile, ticket_num, price, state, buy_time, coupon_price
				from book_table where user_id=:user_id order by book_id desc";
		$exe_params[':user_id']=$_SESSION['user_id'];
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	//$start_time = getMillisecond()-$start_time;
	//error_log("time usered=".$start_time);
	return $result;
	
	//return array(array('book_id'=>'12434543','verify_code'=>'1234','from_city'=>'青岛','to_city'=>'广州','from_stop'=>'中山路','to_stop'=>'客村',
	//			'start_date'=>'2017-09-08','from_time'=>'14:00','contact_mobile'=>'13345676543','ticket_num'=>'2','price'=>'30', 'state'=>'已过期'));
}

function get_coupon_data(){
	
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		
		$exe_params = array();
		$sql = "select user_coupon_id, state, get_time, use_time, start_time, end_time, coupon_price, coupon_describe, route_ids from user_coupon_table, coupon_table where 
				user_coupon_table.coupon_id = coupon_table.coupon_id and 
				user_id=:user_id order by user_coupon_id desc";
		$exe_params[':user_id']=$_SESSION['user_id'];
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$dbh = null;
		
	} catch (Exception $e) {
		error_log($e->getMessage());
	}
	return $result;
	
	//return array(array('book_id'=>'12434543','verify_code'=>'1234','from_city'=>'青岛','to_city'=>'广州','from_stop'=>'中山路','to_stop'=>'客村',
	//			'start_date'=>'2017-09-08','from_time'=>'14:00','contact_mobile'=>'13345676543','ticket_num'=>'2','price'=>'30', 'state'=>'已过期'));
}

//得到用户在此班次下可用的coupon
function get_book_coupon_data(){
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		
		$exe_params = array();
		$sql = "select user_coupon_id, coupon_price, is_special_available from user_coupon_table, coupon_table where 
				user_coupon_table.coupon_id=coupon_table.coupon_id and user_id=:user_id and user_coupon_table.state='正常' and (route_ids like '%>全部>%' or route_ids like :route_id)";
		$exe_params[':user_id']=$_SESSION['user_id'];
		$exe_params[':route_id']='%>'.trim($_SESSION['route_id']).'>%';
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

//得到用户的乘车人列表信息
function get_customers(){
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		
		$exe_params = array();
		$sql = "select names, id_cards, mobiles from customer_table where user_id=:user_id";
		$exe_params[':user_id']=1;//$_SESSION['user_id'];
		
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
	} catch (Exception $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}
?>