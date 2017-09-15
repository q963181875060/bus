<?php

include 'logicController.php';
$VIEW_URL = 'http://139.199.105.54/bus/view/'; 

$req = json_decode( $_POST['request'],1);
if(isset($req['action'])){
        switch ($req['action']){
			//页面跳转
			case "goto_index": 
				if(isset($_SESSION['city_direction'])){
					if($_SESSION['city_direction'] == 0 && isset($req['selected_city'])){
						$_SESSION['from_city'] = trim($req['selected_city']);
						$_SESSION['to_city'] = '请选择';
					}
					if($_SESSION['city_direction'] == 1 && isset($req['selected_city'])){
						$_SESSION['to_city'] = $req['selected_city'];
					}
				}
				$res  = array('url'=>$VIEW_URL.'index.php');
				echo json_encode($res);		
				break;
			case "goto_select_city":
				if(isset($req['city_direction'])){
					$_SESSION['city_direction'] = $req['city_direction'];
					if($req['city_direction'] == 1 && isset($req['from_city'])){
						$_SESSION['from_city'] = trim($req['from_city']);
					}
				}
				$res  = array('url'=>$VIEW_URL.'select_city.php');
				echo json_encode($res);
				break;
			case "goto_select_stop":
				if(isset($req['from_city'])) $_SESSION['from_city'] = trim($req['from_city']);
				if(isset($req['to_city'])) $_SESSION['to_city'] = trim($req['to_city']);
				if(isset($req['start_date'])) $_SESSION['start_date'] = trim($req['start_date']);
				$res  = array('url'=>$VIEW_URL.'select_stop.php');
				echo json_encode($res);
				break;
			case "goto_select_time":
				if(isset($req['from_stop'])) $_SESSION['from_stop'] = trim($req['from_stop']);
				if(isset($req['to_stop'])) $_SESSION['to_stop'] = trim($req['to_stop']);
				if(isset($req['lookup_date'])){
					$_SESSION['lookup_date'] = trim($req['lookup_date']);
				}else{
					$_SESSION['lookup_date'] = trim($_SESSION['start_date']);
				}
				$res  = array('url'=>$VIEW_URL.'select_time.php');
				echo json_encode($res);
				break;
			case "goto_book":
				if(isset($req['from_time'])) $_SESSION['from_time'] = trim($req['from_time']);
				if(isset($req['route_id'])) $_SESSION['route_id'] = trim($req['route_id']);
				if(isset($req['start_date'])) $_SESSION['start_date'] = trim($req['start_date']);
				if(isset($req['is_special_ticket'])) $_SESSION['is_special_ticket'] = trim($req['is_special_ticket']);
				if(isset($req['price'])) $_SESSION['price'] = trim($req['price']);
				$res  = array('url'=>$VIEW_URL.'book.php');
				echo json_encode($res);
				break;
			
			//逻辑处理
			case "cancel_book":
				//除book_id，还要检验user_id
				$suc = 0;
				$msg = '退票失败，请稍后再试';
				try {					
					if(isset($req['book_id'])){
						global $DBSTR, $user, $pass;
						$exe_params = array();
						
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sql = "update book_table set state='已退票' where user_id=:user_id and book_id=:book_id and state='正常'";
						//$exe_params[':user_id']=trim($_SESSION['user_id']);
						$exe_params[':user_id']=1;
						$exe_params[':book_id']=$req['book_id'];
						$sth = $dbh->prepare($sql);
						$suc = $sth->execute($exe_params);
						
						$dbh = null;
					}					
					
				} catch (Exception $e) {
					error_log($e->getMessage());
				}
			
				$res  = array('url'=>$VIEW_URL.'history.php','suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
			case "book_ticket":
				$is_available = 0;//余票是否可用
				$suc = 0;
				$msg = '';
				try {
					if(isset($req['ticket_num'])) $_SESSION['ticket_num'] = trim($req['ticket_num']);
				
					global $DBSTR, $user, $pass;
					$dbh = new PDO ($DBSTR,$user,$pass); 
					$exe_params = array();
					//检查余票数量
					$sql = "select * from
							(
								select * from route_table 
									where route_id=:route_id
							)tab1
							left join
							(
								select route_id route_id1, is_special_ticket, sum(ticket_num) booked_num from book_table
								where 
									route_id=:route_id1 and state='正常' and start_date=:start_date
								group by 
									route_id, is_special_ticket
							)tab2 on (tab1.route_id = tab2.route_id1)
							";
					$exe_params[':route_id']=trim($_SESSION['route_id']);	
					$exe_params[':route_id1']=trim($_SESSION['route_id']);	
					$exe_params[':start_date']=trim($_SESSION['start_date']);	
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					
					$available_special_num = $result[0]['special_ticket_num'];
					$available_num = $result[0]['ticket_num'];
					foreach($result as $unit){
						if($unit['is_special_ticket'] == 1){
							$available_special_num -= $unit['booked_num'];
							$available_num -= $unit['booked_num'];
						}else{
							$available_num -= $unit['booked_num'];
						}
					}
					
					if($_SESSION['is_special_ticket'] == 1 && $_SESSION['ticket_num'] <= $available_special_num && $available_special_num > 0){
						$is_available = 1;
					}
					
					if($_SESSION['is_special_ticket'] == 0 && $_SESSION['ticket_num'] <= $available_num && $available_num > 0){
						$is_available = 1;
					}
					
					if($is_available == 1){//余票够用
						$sql = "insert into book_table (user_id, route_id, start_date, from_time, from_city, to_city, from_stop, to_stop, 
								buy_time, verify_code, is_special_ticket, ticket_num, price, contact_mobile, state)
							values (
								:user_id, :route_id, :start_date, :from_time, :from_city, :to_city, :from_stop, :to_stop, 
								:buy_time, :verify_code, :is_special_ticket, :ticket_num, :price, :contact_mobile, :state
							)";
						$exe_params = array();
						$exe_params[':user_id']=1;//trim($_SESSION['user_id']);	
						$exe_params[':route_id']=trim($_SESSION['route_id']);	
						$exe_params[':start_date']=trim($_SESSION['start_date']);	
						$exe_params[':from_time']=trim($_SESSION['from_time']);	
						$exe_params[':from_city']=trim($_SESSION['from_city']);	
						$exe_params[':to_city']=trim($_SESSION['to_city']);	
						$exe_params[':from_stop']=trim($_SESSION['from_stop']);	
						$exe_params[':to_stop']=trim($_SESSION['to_stop']);	
						date_default_timezone_set('Asia/Shanghai');
						$exe_params[':buy_time']=date("Y-m-d H:i:s");
						$exe_params[':verify_code']=rand(10000,99999);
						$exe_params[':is_special_ticket']=trim($_SESSION['is_special_ticket']);	
						$exe_params[':ticket_num']=trim($_SESSION['ticket_num']);	
						$exe_params[':price']=trim($_SESSION['price']);	
						$exe_params[':contact_mobile']=$result[0]['contact_mobile'];	
						$exe_params[':state']='正常';	
						
						$sth = $dbh->prepare($sql);
						$suc = $sth->execute($exe_params)?1:0;
					}else{
						$msg = '余票不足';
					}
					
					$dbh = null;
					
				} catch (Exception $e) {
					error_log($e->getMessage());
					$msg = '系统错误，请稍后再试';
				}
				$res  = array('url'=>$VIEW_URL.'history.php', 'suc'=>$suc, 'msg'=>$msg);
				echo json_encode($res);
				break;
		}
}else{
        
}
?>

