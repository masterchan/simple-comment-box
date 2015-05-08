<?php
header("Access-Control-Allow-Origin: *");


if (preg_match("/^[0-9]{1,20}$/", $_GET['id'])&&
	preg_match("/^[0-9]{1,20}$/", $_GET['cp'])) {
	require('db-comment.php');
	$mysql = new PDO(_PDO_Connect_String_, _PDO_username_, _PDO_password_);

	$mission = $mysql->prepare("SELECT * FROM `comment_statistic` WHERE `pageid` = ?;");
	if ($mission->execute(array(
			$_GET['id']
		))) {
		$statistic_data = $mission->fetch(PDO::FETCH_OBJ);
		if (empty($statistic_data)) {
			echo "no comment";
		} else {
			$mission = $mysql->prepare("SELECT * FROM `comment_cache` WHERE `pageid` = ? AND `c_pageid` =?;");
			if ($mission->execute(array(
					$_GET['id'],
					$_GET['cp']
				))) {
					$comment_data = $mission->fetch(PDO::FETCH_OBJ);
					echo $statistic_data->commentcount.'|'.$statistic_data->pagecount.'|'.$comment_data->content;
				} else {
					echo "internal error";
				}
		}
	} else {
		echo "internal error";
	}
} else {
	echo "error";
}
?>
