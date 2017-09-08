<?php
$res = array('a');
if(isset($_POST['request']))
{
    echo json_encode($res);
    error_log('suc');
}else{
    error_log('fail');
}


?>
