<html>
<head>
<script type="text/javascript" src="common.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

<script>
wx.config({
    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: 'wxeb32137da1f48642', // 必填，公众号的唯一标识
    timestamp: , // 必填，生成签名的时间戳
    nonceStr: '', // 必填，生成签名的随机串
    signature: '',// 必填，签名，见附录1
    jsApiList: [] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});
        
</script>

</head>
<body>
<?php
	$obj = array();
	$obj['first'] = 'val1';
	$obj['second'] = array();
	$obj['second']['s1'] = 's1';
	$obj['second']['s2'] = 's2';
	echo json_encode($obj);
	
?> 

<!--<a onclick="test()">change it</a>-->
</body>
</html>