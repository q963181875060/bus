<?php
require_once 'common.php';
require_once 'wechat_poster.php';
require_once 'wxpay/lib/WxPay.Api.php';
require_once 'wxpay/example/WxPay.JsApiPay.php';

date_default_timezone_set('Asia/Shanghai');

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
				$res  = array('url'=>'index.php');
				echo json_encode($res);		
				break;
			case "goto_select_city":
				if(isset($req['city_direction'])){
					$_SESSION['city_direction'] = $req['city_direction'];
					if($req['city_direction'] == 1 && isset($req['from_city'])){
						$_SESSION['from_city'] = trim($req['from_city']);
					}
				}
				$res  = array('url'=>'select_city.php');
				echo json_encode($res);
				break;
			case "goto_select_stop":
				if(isset($req['from_city']) && isset($req['to_city']) && isset($req['start_date'])){
					$_SESSION['from_city'] = trim($req['from_city']);
					$_SESSION['to_city'] = trim($req['to_city']);
					$_SESSION['start_date'] = trim($req['start_date']);
					$suc = 1;
					$msg = '';
				}else{
					$suc = 0;
					$msg = "参数上传不全";
				}
				
				$res  = array('url'=>'select_stop.php', 'suc'=>$suc, 'msg'=>$msg);
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
				$res  = array('url'=>'select_time.php');
				echo json_encode($res);
				break;
			case "goto_book":
				if(isset($req['from_time'])) $_SESSION['from_time'] = trim($req['from_time']);
				if(isset($req['route_id'])) $_SESSION['route_id'] = trim($req['route_id']);
				if(isset($req['start_date'])) $_SESSION['start_date'] = trim($req['start_date']);
				$res  = array('url'=>'book.php');
				echo json_encode($res);
				break;
			
			//逻辑处理
			case "post_survey":
				try {					
					$survey_sheet_name = $req['survey_sheet_name'];
					$ps = $req['ps'];
					
					$dbh=new PDO($DBSTR,$user,$pass, array(PDO::ATTR_AUTOCOMMIT=>0)); #一定要关闭自动提交
					$dbh->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION); #开启异常模式
					
					$dbh->beginTransaction();
					
					$sql = "select * from survey_table where user_id=:user_id and survey_sheet_name=:survey_sheet_name";
					$exe_params = array();
					$exe_params[':survey_sheet_name']=$survey_sheet_name;
					$exe_params[':user_id']=$_SESSION['user_id'];
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					
					if(count($result) != 0){
						throw new Exception("已经提交，请勿重复提交");
					}
					
					$sql = "insert into survey_table (survey_sheet_name, user_id, problem_id, problem_title, problem_value) values ";
					for($i = 0; $i<count($ps); $i++){
						$sql = $sql . '(:survey_sheet_name' . $i . ',:user_id' . $i . ',:problem_id' . $i . ',:problem_title' . $i . ',:problem_value' . $i . ')';
						if($i != count($ps)-1){
							$sql = $sql . ',';
						}
					}
					$exe_params = array();
					for($i = 0; $i<count($ps); $i++){
						$exe_params[':survey_sheet_name'.$i]=$survey_sheet_name;
						$exe_params[':user_id'.$i]=$_SESSION['user_id'];
						$exe_params[':problem_id'.$i]=$i;
						$exe_params[':problem_title'.$i]=$ps[$i]['problem_title'];
						$exe_params[':problem_value'.$i]=$ps[$i]['problem_value'];
					}
					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("提交调查问卷进入数据库失败，请稍后再试");
					}
					
					//插入代金券进入数据库
					$sql = "insert into user_coupon_table (user_id, coupon_id, state, get_time) values (:user_id, :coupon_id, :state, :get_time)";
					$exe_params = array();
					$exe_params[':user_id']=$_SESSION['user_id'];
					$exe_params[':coupon_id']=3;
					$exe_params[':state']='正常';
					$exe_params[':get_time']=date("Y-m-d H:i:s");
					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("插入代金券进入数据库失败，请稍后再试");
					}
					
					//发送赠送代金券模板消息
					$data[] = array();
					$data['user_id'] = $_SESSION['user_id'];
					$data['first'] = '领取代金券成功！';
					send_get_coupon_template($data);
				
					$suc = 1;
					$msg = '领取代金券成功!';
					$dbh->commit();
					
				} catch (Exception $e) {
					$dbh->rollback();
					$suc = 0;
					$msg = $e->getMessage();
				}finally{
					$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
					$dbh = null;
				}
				$res  = array('url'=>'history.php','suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
			case "cancel_book":
				try {					
					if(isset($req['book_id'])){
						
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sql = "update book_table set state='已退票', refund_time=:refund_time where user_id=:user_id and book_id=:book_id and state='正常'";
						$exe_params = array();
						$exe_params[':user_id']=$_SESSION['user_id'];
						$exe_params[':refund_time']=date("Y-m-d H:i:s");
						$exe_params[':book_id']=$req['book_id'];
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
						$affected_rows = $sth->rowCount();
						if($affected_rows == 0){
							throw new Exception("更新数据库失败，请稍后再试");
						}
						
						$sql = "select * from book_table where book_id=:book_id";
						$exe_params = array();
						$exe_params[':book_id']=$req['book_id'];
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
						$result = $sth->fetchAll(PDO::FETCH_ASSOC);
						if(count($result) == 0){
							throw new Exception("获取数据库信息失败，请稍后再试");
						}
						
						//对用户的账户发起退款
						$out_trade_no = $result[0]['book_id'];
						$total_fee = (int)(((float)$result[0]['price'] * (int)$result[0]['ticket_num'] - (float)$result[0]['coupon_price'])*100);
						$refund_fee = round($total_fee*0.95, 0);
						$input = new WxPayRefund();
						$input->SetOut_trade_no($out_trade_no);
						$input->SetTotal_fee($total_fee);
						$input->SetRefund_fee($refund_fee);
						$input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
						$input->SetOp_user_id(WxPayConfig::MCHID);
						$input->SetRefund_account('REFUND_SOURCE_UNSETTLED_FUNDS');//REFUND_SOURCE_RECHARGE_FUNDS
						$res = WxPayApi::refund($input);
						//error_log(print_r($res));
						
						//如果未结算余额不足，使用账户余额退款
						if(isset($res['err_code']) && $res['err_code'] == 'NOTENOUGH'){
							$input->SetRefund_account('REFUND_SOURCE_RECHARGE_FUNDS');
							$res = WxPayApi::refund($input);						
						}
						
						if(!(isset($res['result_code']) && $res['result_code'] == 'SUCCESS')){
							if(isset($res['err_code'])){
								throw new Exception("退票失败，请联系工作人员 ".$res['err_code'].$res['err_code_des']);
							}else{
								throw new Exception("退票失败，请联系工作人员 ".$res['return_msg']);
							}
						}
						
						//发送成功退票模板消息
						$data[] = array();
						$data['user_id'] = trim($_SESSION['user_id']);
						$data['first'] = '取消订单成功！';
						$data['keyword1'] = $result[0]['book_id'];
						$data['keyword2'] = '用户申请';
						$data['keyword3'] = '￥' . round(((float)$refund_fee)/100,2);
						$data['remark'] = '有疑问欢迎随时联系我们，欢迎下次乘坐合力巴士！';
						send_cancel_ticket_template($data);
					
						$suc = 1;
						$msg = '';
					}else{
						throw new Exception("参数提交错误，请稍候再试！");
					}					
				} catch (Exception $e) {
					error_log($e->getMessage());
					$suc = 0;
					$msg = $e->getMessage();
				}finally{
					$dbh = null;
				}
				$res  = array('url'=>'history.php','suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
			case "book_ticket"://提交订单
				$suc = 0;
				$msg = '';
				$coupon_price = 0;
				try {
					if(isset($req['ticket_num'])) $_SESSION['ticket_num'] = trim($req['ticket_num']);
					if(isset($req['is_special_ticket'])) $_SESSION['is_special_ticket'] = trim($req['is_special_ticket']);
				
					$dbh=new PDO($DBSTR,$user,$pass, array(PDO::ATTR_AUTOCOMMIT=>0)); #一定要关闭自动提交
					$dbh->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION); #开启异常模式
					
					$dbh->beginTransaction();
					
					//获取票的单价
					$sql = "select * from route_table where route_id=:route_id";
					$exe_params = array();
					$exe_params[':route_id']=trim($_SESSION['route_id']);					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					//error_log("count:"+count($result));
					if(count($result) != 0){
						if($_SESSION['is_special_ticket'] == 1){
							$price = $result[0]['special_price'];
						}else{
							$price = $result[0]['price'];
						}
					}else{
						throw new Exception("班次错误");
					}
				
					if(isset($req['user_coupon_id'])){
						//若使用了代金券，检查代金券是否可用,交的钱是否是正确的
						$sql = "select coupon_price from user_coupon_table, coupon_table where user_coupon_table.coupon_id = coupon_table.coupon_id and state='正常' and user_id= :user_id and user_coupon_id = :user_coupon_id";
						$exe_params = array();
						$exe_params[':user_coupon_id']=trim($req['user_coupon_id']);	
						$exe_params[':user_id']=trim($_SESSION['user_id']);
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
						$result = $sth->fetchAll(PDO::FETCH_ASSOC);
						//error_log("count:"+count($result));
						if(count($result) != 0){
							$coupon_price  = $result[0]['coupon_price'];
						}else{
							throw new Exception("代金券信息错误");
						}
					}else{//没用代金券
						$coupon_price  = 0;
					}
					$total_price = round($_SESSION['ticket_num'] * $price - $coupon_price, 2);
					
					//检查余票数量
					$exe_params = array();
					$sql = "select * from
							(
								select * from route_table 
									where route_id=:route_id
							)tab1
							left join
							(
								select route_id route_id1, is_special_ticket, sum(ticket_num) booked_num from book_table
								where 
									route_id=:route_id1 and state in ('正常', '待支付', '待验票', '已过期', '已验票') and start_date=:start_date
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
					if($available_special_num > $available_num)  $available_special_num = $available_num;
					
					if($_SESSION['is_special_ticket'] == 1 && $_SESSION['ticket_num'] > $available_special_num && $available_special_num >= 0){
						
						throw new Exception("余票不足");
					}
					
					if($_SESSION['is_special_ticket'] == 0 && $_SESSION['ticket_num'] > $available_num && $available_num >= 0){
						throw new Exception("余票不足");
					}
					
					//插入购票信息到book_table
					$sql = "insert into book_table (user_id, route_id, start_date, from_time, from_city, to_city, from_stop, to_stop, 
							submit_time, verify_code, is_special_ticket, ticket_num, price, contact_mobile, state, user_coupon_id, coupon_price)
						values (
							:user_id, :route_id, :start_date, :from_time, :from_city, :to_city, :from_stop, :to_stop, 
							:submit_time, :verify_code, :is_special_ticket, :ticket_num, :price, :contact_mobile, :state, :user_coupon_id, :coupon_price
						)";
					$exe_params = array();
					$exe_params[':user_id']=trim($_SESSION['user_id']);	
					$exe_params[':route_id']=trim($_SESSION['route_id']);	
					$exe_params[':start_date']=trim($_SESSION['start_date']);	
					$exe_params[':from_time']=trim($_SESSION['from_time']);	
					$exe_params[':from_city']=trim($_SESSION['from_city']);	
					$exe_params[':to_city']=trim($_SESSION['to_city']);	
					$exe_params[':from_stop']=trim($_SESSION['from_stop']);	
					$exe_params[':to_stop']=trim($_SESSION['to_stop']);	
					
					$exe_params[':submit_time']=date("Y-m-d H:i:s");;
					$exe_params[':verify_code']=rand(10000,99999);
					$exe_params[':is_special_ticket']=trim($_SESSION['is_special_ticket']);	
					$exe_params[':ticket_num']=trim($_SESSION['ticket_num']);	
					$exe_params[':price']=trim($price);	
					$exe_params[':contact_mobile']=$result[0]['contact_mobile'];	
					$exe_params[':state']='待支付';	
					$exe_params[':user_coupon_id']=isset($req['user_coupon_id'])?$req['user_coupon_id']:'';	
					$exe_params[':coupon_price']=isset($req['user_coupon_id'])?$coupon_price:0;
					
					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("购票信息提交失败，请稍后再试");
					}
				
					$_SESSION['book_id'] = $dbh->lastInsertId('book_id');
					
					//获取微信服务器预定订单信息并返回给前端
					//①、获取用户openid
					$tools = new JsApiPay();
					$openId = $_SESSION['user_id'];
					
					//②、统一下单
					$input = new WxPayUnifiedOrder();
					$input->SetBody("test");
					$input->SetAttach("test");
					$input->SetOut_trade_no($_SESSION['book_id']);
					$input->SetTotal_fee($total_price*100);
					$input->SetTime_start(date("YmdHis"));
					$input->SetTime_expire(date("YmdHis", time() + 600));
					$input->SetGoods_tag("test");
					$input->SetNotify_url("http://helibus.cn/bus/view/wechat_server.php");
					$input->SetTrade_type("JSAPI");
					$input->SetOpenid($openId);
					$order = WxPayApi::unifiedOrder($input);
					//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
					$jsApiParameters = $tools->GetJsApiParameters($order);
					$dbh->commit();
					
					$suc = 1;
					$msg = $jsApiParameters;
				} catch (Exception $e) {
					//error_log("roll_back");
					$dbh->rollback();
					$msg = $e->getMessage();
					error_log($msg);
					$suc = 0;
				} finally{
					$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
					$dbh = null;
				}
				
				$res  = array('url'=>'history.php', 'suc'=>$suc, 'msg'=>$msg);
				echo json_encode($res);
				break;
			case 'book_ticket_success':
				$suc = 0;
				$msg = '';
				try {
					if(!isset($_SESSION['book_id'])){
						throw new Exception("订单信息有误，请电话联系合力巴士");
					}
					
					$dbh=new PDO($DBSTR,$user,$pass, array(PDO::ATTR_AUTOCOMMIT=>0)); #一定要关闭自动提交
					$dbh->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION); #开启异常模式
					
					$dbh->beginTransaction();
					
					//插入购票信息到book_table
					$sql = "update book_table set state='正常', buy_time=:buy_time where book_id=:book_id and user_id=:user_id and state='待支付'";
					$exe_params = array();
					$exe_params[':buy_time']=date("Y-m-d H:i:s");
					$exe_params[':book_id']=trim($_SESSION['book_id']);
					$exe_params[':user_id'] = $_SESSION['user_id'];
					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("支付信息更新失败，请联系工作人员");
					}
					
					
					$sql = "select * from book_table where book_id=:book_id";
					$exe_params = array();
					$exe_params[':book_id']=trim($_SESSION['book_id']);	
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					if(count($result) == 0){
						throw new Exception("拉取订单信息失败，请联系工作人员");
					}
					$book = $result[0];
					
					//发送成功购票模板消息
					$data[] = array();
					$data['user_id'] = trim($book['user_id']);
					$data['first'] = '恭喜购票成功！';
					$data['productType'] = '上车地点';
					$data['name'] = $book['from_stop'];
					$data['time'] = $book['start_date'] . ' ' .$book['from_time'];
					$data['result'] = '订单号'.$book['book_id'].'，验票码'.$book['verify_code'];
					$data['remark'] = '请提前15分钟在上车点等候上车，如有问题请联系领队，电话为：'.$book['contact_mobile'];
					send_book_ticket_template($data);
					
					//设置代金券已使用
					if($book['user_coupon_id'] != null && $book['user_coupon_id'] != 0){
						$sql = "update user_coupon_table set state = '已使用', use_time=:use_time where user_coupon_id=:user_coupon_id";
						$exe_params = array();
						$exe_params[':use_time']=date("Y-m-d H:i:s");
						$exe_params[':user_coupon_id']=$book['user_coupon_id'];
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
						$affected_rows = $sth->rowCount();
						if($affected_rows == 0){
							throw new Exception("代金券更新错误，请联系工作人员");
						}
					}
					
					$dbh->commit();
					
					$suc = 1;
					$msg = '';
				} catch (Exception $e) {
					//error_log("roll_back");
					$dbh->rollback();
					$msg = $e->getMessage();
					$suc = 0;
				} finally{
					$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
					$dbh = null;
					
				}
				
				$res  = array('url'=>'history.php', 'suc'=>$suc, 'msg'=>$msg);
				echo json_encode($res);
				break;
			case 'book_ticket_cancel':
				$suc = 0;
				$msg = '';
				try {
					if(!isset($_SESSION['book_id'])){
						throw new Exception("订单信息有误，请电话联系合力巴士");
					}
					
					$dbh=new PDO($DBSTR,$user,$pass, array(PDO::ATTR_AUTOCOMMIT=>0)); #一定要关闭自动提交
					$dbh->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION); #开启异常模式
					
					$dbh->beginTransaction();
					
					//插入购票信息到book_table
					$sql = "update book_table set state='已取消' where book_id=:book_id and user_id=:user_id and state='待支付'";
					$exe_params = array();
					$exe_params[':book_id']=trim($_SESSION['book_id']);
					$exe_params[':user_id'] = $_SESSION['user_id'];
					
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("取消订单失败");
					}
					
					$dbh->commit();
					
					$suc = 1;
					$msg = '取消订单成功';
				} catch (Exception $e) {
					//error_log("roll_back");
					$dbh->rollback();
					$msg = $e->getMessage();
					$suc = 0;
				} finally{
					$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
					$dbh = null;
				}
				
				$res  = array('url'=>'history.php', 'suc'=>$suc, 'msg'=>$msg);
				echo json_encode($res);
				break;
			case "change_customer":
				try {					
					$dbh=new PDO($DBSTR,$user,$pass, array(PDO::ATTR_AUTOCOMMIT=>0)); #一定要关闭自动提交
					$dbh->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION); #开启异常模式
					$dbh->beginTransaction();
					
					$sql = "select * from customer_table where user_id=:user_id";
					$exe_params = array();
					$exe_params[':user_id']=1;//$_SESSION['user_id'];
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					if(count($result) > 0){
						$sql = "update customer_table set names=:names, id_cards=:id_cards where user_id=:user_id";
						$exe_params = array();
						$exe_params[':names']=$req['names'];
						$exe_params[':id_cards']=$req['id_cards'];
						$exe_params[':user_id']=1;//$_SESSION['user_id'];
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
					}else{
						//新用户需要插入信息
						$sql = "insert into customer_table (user_id, names, id_cards) values (:user_id, :names, :id_cards)";
						$exe_params = array();
						$exe_params[':names']=$req['names'];
						$exe_params[':id_cards']=$req['id_cards'];
						$exe_params[':user_id']=1;//$_SESSION['user_id'];
						$sth = $dbh->prepare($sql);
						$sth->execute($exe_params);
						$affected_rows = $sth->rowCount();
							if($affected_rows == 0){
								throw new Exception("信息更新失败，请稍后再试");
							}
					}
				
					$suc = 1;
					$msg = '成功!';
					$dbh->commit();
					
				} catch (Exception $e) {
					$dbh->rollback();
					$suc = 0;
					$msg = $e->getMessage();
				}finally{
					$dbh->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
					$dbh = null;
				}
				$res  = array('url'=>'customer.php','suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
		}
}else{
        
}
?>

