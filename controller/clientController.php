<?php
$VIEW_URL = 'http://139.199.105.54/bus/view/'; 

$req = json_decode( $_POST['request'],1);
if(isset($req['action'])){
		error_log('have action');
        switch ($req['action']){
			//页面跳转
			case "goto_index": 
				error_log('goto_index');
				if(isset($_SESSION['city_direction'])){
					error_log($req['city_direction']);
					if($_SESSION['city_direction'] == 0 && isset($req['selected_city'])){
						error_log('set selected city');
						$_SESSION['from_city'] = $req['selected_city'];
					}
					if($_SESSION['city_direction'] == 1 && isset($req['selected_city'])){
						error_log('set selected city');
						$_SESSION['to_city'] = $req['selected_city'];
					}
				}
				$res  = array('url'=>$VIEW_URL.'index.php');
				echo json_encode($res);		
				break;
			case "goto_select_city":
				error_log('goto_select_city');
				if(isset($req['city_direction'])){
					$_SESSION['city_direction'] = $req['city_direction'];
					if($req['city_direction'] == 1 && isset($req['from_city'])){
						$_SESSION['from_city'] = $req['from_city'];
					}
				}
				$res  = array('url'=>$VIEW_URL.'select_city.php');
				echo json_encode($res);
				break;
			case "goto_select_stop":
				error_log('goto_select_stop');
				if(isset($req['from_city'])) $_SESSION['from_city'] = $req['from_city'];
				if(isset($req['to_city'])) $_SESSION['to_city'] = $req['to_city'];
				if(isset($req['start_date'])) $_SESSION['start_date'] = $req['start_date'];
				$res  = array('url'=>$VIEW_URL.'select_stop.php');
				echo json_encode($res);
				break;
			case "goto_select_time":
				error_log('goto_select_time');
				if(isset($req['from_stop'])) $_SESSION['from_stop'] = $req['from_stop'];
				if(isset($req['to_stop'])) $_SESSION['to_stop'] = $req['to_stop'];
				if(isset($req['change_date'])) $_SESSION['start_date'] = $_SESSION['start_date'];
				$res  = array('url'=>$VIEW_URL.'select_time.php');
				echo json_encode($res);
				break;
			case "goto_book":
				if(isset($req['from_time'])) $_SESSION['from_time'] = $req['from_time'];
				$res  = array('url'=>$VIEW_URL.'book.php');
				echo json_encode($res);
				break;
				
			//逻辑处理
			case "cancel_book":
				$res  = array('url'=>$VIEW_URL.'history.php');
				echo json_encode($res);
				break;
			/*case "index_get_data":
				error_log('index_get_data');
				error_log($_SESSION['from_city']);
				if(!isset($_SESSION['from_city']) || $_SESSION['from_city'] == ''){
					$_SESSION['from_city'] = 'no';
				}
				if(!isset($_SESSION['to_city']) || $_SESSION['to_city']==''){
					$_SESSION['to_city'] = 'no';
				}
				$res = array('from_city'=>$_SESSION['from_city'],'to_city'=>$_SESSION['to_city']);
				echo json_encode($res);
				break;
			case "select_city_get_data":
				//error_log('select_city_get_data');
				$res = array();
				if(isset($_SESSION['city_direction']) && $_SESSION['city_direction'] == 0){
					$res = array('city_direction'=>$_SESSION['city_direction'],'from_city'=>'no','cities'=>array('weifang','jinan'));
				}else{
					$res = array('city_direction'=>$_SESSION['city_direction'],'from_city'=>'no','cities'=>array('guangzhou'));
				}
				
                echo json_encode($res);
				//error_log('get_cities');
				break;
			*/
		}

        /*if($req['action']=='cities'){
                error_log('cities');
                if(isset($req['from_city'])){
                        //return end cities
                        $res = array('潍坊','济宁');
                        echo json_encode($res);
                        error_log('from_city');
                }else{
                        //return start cities
                        $res = array('青岛','济南');
                        echo json_encode($res);
                        error_log('no from city');
                }   
        }else if($req['action']=='get_current_city'){
                $res = array('青岛');
                echo json_encode($res);
                error_log("get current city");
        }*/
}else{
        error_log('no action');
}
//error_log('end');
//error_log($str['action']);
//$arr = array ('a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5);
//echo json_encode($arr);

?>

