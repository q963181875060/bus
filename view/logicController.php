<?php
//返回两城市间的停靠点
function get_select_stop_data(){
	return array('from_stops'=>array('始发站1','始发站2'), 'to_stops'=>array('终点站1','终点站2'));
}

//返回两停靠点的某天的班车安排
function get_select_time_data(){
	//$routes['from_stop'],$routes['to_stop'],$routes['price'],$routes['from_time']
	//return array(array('from_stop'=>$from_stop, 'to_stop'=>$to_stop, 'price'=>"35", 'from_time'=>"14:00"));
	return array('start_date'=>'2017-09-08','routes'=>array(array('from_stop'=>'青岛', 'to_stop'=>'广州', 'price'=>"35", 'from_time'=>"14:00")));
}

//返回待预定车票的信息
function get_book_data(){
	return array('from_city'=>'青岛','to_city'=>'广州','from_stop'=>'中山路','to_stop'=>'客村','start_date'=>'2017-09-08',
				'from_time'=>'14:00','price'=>'35');
}

function get_history_data(){
	return array(array('book_id'=>'12434543','verify_code'=>'1234','from_city'=>'青岛','to_city'=>'广州','from_stop'=>'中山路','to_stop'=>'客村',
				'start_date'=>'2017-09-08','from_time'=>'14:00','contact_mobile'=>'13345676543','ticket_num'=>'2','price'=>'30', 'state'=>'已过期'));
}
?>