<?php

include 'common.php';

try {
		
	date_default_timezone_set('Asia/Shanghai');
	$dbh = new PDO ($DBSTR,$user,$pass); 
	
	$exe_params = array();
	$sql = "update user_coupon_table, coupon_table set state='已过期' where user_coupon_table.coupon_id=coupon_table.coupon_id and state='正常' and 
			str_to_date(end_time,'%Y-%m-%d') < str_to_date(:current_time,'%Y-%m-%d')";
	$exe_params[':current_time']=date('Y-m-d');
	
	$sth = $dbh->prepare($sql);
	$sth->execute($exe_params);
	
	$dbh = null;
	echo 'update coupon: '.date('Y-m-d H:i:s');
} catch (Exception $e) {
	error_log($e->getMessage());
}

?>

