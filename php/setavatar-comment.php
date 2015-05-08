<?php
header("Access-Control-Allow-Origin: *");
if ((!empty($_POST['uid']))&&
	(preg_match("/^[0-9]{1,20}$/", $_POST['uid']))&&
	(preg_match('@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@', $_POST['target']))) {
	require('db-comment.php');
	$mysql = new PDO(_PDO_Connect_String_, _PDO_username_, _PDO_password_);
	$mission = $mysql->prepare("SELECT `token` FROM `user_table` WHERE id = ?;");
	if ($mission->execute(array($_POST['uid']))) {
		$loginResult = $mission->fetch(PDO::FETCH_OBJ);
		if ($loginResult == NULL) {
			echo "bad token";
			exit();
		} else {
			if ($loginResult->token == $_POST['token']) {
				$user_id = $_POST['uid'];
				$mission = $mysql->prepare("UPDATE `note_comment`.`user_table` SET `avatar_url` = ? WHERE `user_table`.`id` = ?;");
				if ($mission->execute(array(
						$_POST['target'],
						$_POST['uid']
					))) {
					echo "success";
				}else{
					echo "internal error";
				}
			} else {
				echo "bad token";
				exit();
			}
		}
	}
}
?>
