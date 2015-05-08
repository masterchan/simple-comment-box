<?php
header("Access-Control-Allow-Origin: *");


if ((!empty($_POST['uid']))&&(preg_match("/^[0-9]{1,20}$/", $_POST['uid']))) {
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
			} else {
				echo "bad token";
				exit();
			}
		}
	}
}

////////////
if (preg_match("/^[0-9]{1,20}$/", $_POST['pageid'])&&
	preg_match("/^[0-9]{1,20}$/", $_POST['parentid'])&&
	preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\w\W]{10,250}$/u", $_POST['comment'])&&
		(
			(
				(!isset($user_id))&&
				(preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{3,20}$/u", $_POST['name']))&&
				(preg_match("/^[0-9a-z][a-z0-9\._-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$/", $_POST['email']))
			)||
			(
				(isset($user_id))&&
				(!isset($_POST['name']))&&
				(!isset($_POST['email']))
			)
		)
	) {
	$mysql = new PDO(_PDO_Connect_String_, _PDO_username_, _PDO_password_);
	$mission = $mysql->prepare("INSERT INTO `note_comment`.`comment_table` (`id`, `page_id`, `parent_id`, `user_id`, `user_name`, `user_email`, `submit_time`, `comment`, `ipaddress`) VALUES (NULL, ?, ?, ?, ?, ?, NOW(), ?, ?);");
		$commentText = str_replace(array(
				"<",
				">",
				"=\"",
				"='",
				"eval",
				"<!--",
				"|",
				"-->"
			),"-",$_POST['comment']);
		$commentText = htmlspecialchars($commentText);
		$commentText = str_replace(array(
				"[ln]",
				"[b]",
				"[/b]",
				"[d]",
				"[/d]"
			),array(
				"<br />",
				"<strong>",
				"</strong>",
				"<del>",
				"</del>"
			),$commentText);
		//Insert Comment
		if (isset($user_id)) {
			$request_array = array(
				$_POST['pageid'],
				$_POST['parentid'],
				$user_id,
				NULL,
				NULL,
				nl2br($commentText),
				$_SERVER["REMOTE_ADDR"]
			);
		} else {
			$request_array = array(
				$_POST['pageid'],
				$_POST['parentid'],
				NULL,
				$_POST['name'],
				$_POST['email'],
				nl2br($commentText),
				$_SERVER["REMOTE_ADDR"]
			);
		}

		if ($mission->execute($request_array)) {
			$lastInserId = $mysql->lastInsertId();
			if ($lastInserId == 0) {
				echo "internal error";
				exit();
			}else{
				$mission = $mysql->prepare("SELECT `comment_table`.`id`, `comment_table`.`parent_id`, `comment_table`.`user_name`, `user_table`.`account_name`, `comment_table`.`submit_time`, `comment_table`.`comment` FROM `comment_table` LEFT JOIN `user_table` ON `comment_table`.`user_id` = `user_table`.`id` WHERE `comment_table`.`id` = ?");
				$mission->execute(array($lastInserId));
				$return_data = $mission->fetch(PDO::FETCH_OBJ);
				echo "success|".json_encode($return_data);
			}
		
		//rebuild cache
		$mission = $mysql->prepare("SELECT `comment_table`.`id`, `comment_table`.`parent_id`, `comment_table`.`user_name`, `user_table`.`account_name`, `comment_table`.`submit_time`, `comment_table`.`comment` FROM `comment_table` LEFT JOIN `user_table` ON `comment_table`.`user_id` = `user_table`.`id` WHERE `comment_table`.`page_id` = ? ORDER BY `comment_table`.`submit_time` DESC;");
		if ($mission->execute(array(
				$_POST['pageid']
			))) {
			$cache_data = $mission->fetchAll(PDO::FETCH_OBJ);
			$h = count($cache_data);

			$cache_data = array_chunk($cache_data,8);
			$k = count($cache_data);

			for ($i=0; $i < $k; $i++) {
				$mission = $mysql->prepare("UPDATE `note_comment`.`comment_cache` SET `content` = ? WHERE `comment_cache`.`pageid` = ? AND `comment_cache`.`c_pageid` = ?;");
				if (($mission->execute(array(
						json_encode($cache_data[$i]),
						$_POST['pageid'],
						$i
					)))&&
					($mission->rowCount()==0)
					) {
						$mission = $mysql->prepare("INSERT INTO `note_comment`.`comment_cache` (`pageid`, `c_pageid`, `content`) VALUES (?, ?, ?);");
						$mission->execute(array(
							$_POST['pageid'],
							$i,
							json_encode($cache_data[$i])
						));
				}
			}

			$mission = $mysql->prepare("INSERT INTO `note_comment`.`comment_statistic` (`pageid`, `pagecount`, `commentcount`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `pagecount` = ?, `commentcount` = `commentcount` + 1;");
			$mission->execute(array(
				$_POST['pageid'],
				$k,
				$h,
				$k
			));
		}
	}else{
		echo "error";
	}
}else{
	echo "format error";
}
?>
