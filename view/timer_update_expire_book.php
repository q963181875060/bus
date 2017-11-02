<?php

include 'common.php';

try {
		
	date_default_timezone_set('Asia/Shanghai');
	$dbh = new PDO ($DBSTR,$user,$pass); 
	
	$exe_params = array();
	$sql = "update book_table set state='已取消' where state='待支付' and UNIX_TIMESTAMP(submit_time) < UNIX_TIMESTAMP(:submit_time)";
	$exe_params[':submit_time']=date("Y-m-d H:i:s",strtotime("-5 minutes"));
	//$exe_params[':submit_time']=date("Y-m-d H:i:s");
	
	$sth = $dbh->prepare($sql);
	$sth->execute($exe_params);
	
	
	echo 'update expire book: '.date('Y-m-d H:i:s');
} catch (Exception $e) {
	error_log($e->getMessage());
}finally{
	$dbh = null;
}

?>

