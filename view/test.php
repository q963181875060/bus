<html>
<head>
<script type="text/javascript" src="common.js"></script>
<script type="text/javascript">
    function test(){
		alert("changed");
		test = "changed";
		window.location.href="test1.html";
	}    
    
</script>
</head>
<body>
<?php
$para="hello body";
echo $para;
?> 
test
<!--<a onclick="test()">change it</a>-->
</body>
</html>