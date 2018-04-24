<!DOCTYPE html>
<html>
<head>
	<title>izmaiņu publicētājs</title>
	<meta charset="utf8"/>
</head>
<body>
<?php
if (!empty($_POST)) {
	file_put_contents(dirname(__FILE__)."/needs_update.txt", "");
	echo "atjaunošana pieprasīta<br/>";
?>
<script>
setTimeout(function(){ window.location = "update.php"; }, 1500);
</script>
<?php
}
 
?>
<form method="post" action="">
<input type="submit" name="submit" value="Atjaunot"/>
</form>
* atjaunošana notiek ik pēc minūtes
 
<?php
$output = file_get_contents(dirname(__FILE__)."/update.log");
?>
 
Output:<br/>
<div style="overflow:scroll;width:100%;height:90%">
<pre>
<?=$output?>
</pre>
</div>
 
</body>
</html>