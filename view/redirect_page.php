<html>
<head>
</head>
<body>
<?php
	//由于未关注服务号的付款不会自动关注服务号
	//此文件用于分享给好友或者分享到朋友圈的时候，页面需要跳转到此php页面进行mp页面的中转
	
	Header("HTTP/1.1 303 See Other"); 
	Header("Location: http://mp.weixin.qq.com/s?__biz=MzU4MjIzNDAwMg==&mid=100000001&idx=1&sn=7118b2c958a3e4688bf6eb980947e606&chksm=7dba3bfc4acdb2ea342f0b932abe188d9e99bf45a591e96ca96874cd6a46640c3d172580c067#rd"); 
	exit;
	
?> 

<!--<a onclick="test()">change it</a>-->
</body>
</html>