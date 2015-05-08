<?php
header("Access-Control-Allow-Origin: *");


$defaultURL = "https://api.ink.moe/img/default.jpg";
if (!empty($_GET['a'])) {
	require('db-comment.php');
	$mysql = new PDO(_PDO_Connect_String_, _PDO_username_, _PDO_password_);
	$mission = $mysql->prepare("SELECT `avatar_url` FROM `user_table` WHERE `account_name` = ?;");
	if ($mission->execute(array($_GET['a']))) {
		$data = $mission->fetch(PDO::FETCH_OBJ);
		if (!empty($data->avatar_url)) {
			header("Location: ".$data->avatar_url);
			exit();
		}
	}
}
header("Location: ".$defaultURL);
?>
