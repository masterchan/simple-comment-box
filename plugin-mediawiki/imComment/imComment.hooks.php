<?php
class imCommentHooks {
	public static function onSkinAfterContent( &$data, Skin $skin ) {
		$pageTitle = $skin->getTitle();
		$output = $skin->getOutput();
		$request = $skin->getRequest();

		if ( $pageTitle->isSpecialPage()
			|| $pageTitle->getArticleID() == 0
			|| !$pageTitle->canTalk()
			|| $pageTitle->isTalkPage()
			|| method_exists( $pageTitle, 'isMainPage' ) && $pageTitle->isMainPage()
			|| in_array( $pageTitle->getNamespace(), array( NS_MEDIAWIKI, NS_TEMPLATE, NS_CATEGORY, NS_FILE, NS_USER ))
			|| $output->isPrintable()
			|| $request->getVal( 'action', 'view' ) != 'view' 
			) {

			return true;
		}
		$aID = $pageTitle->getArticleID();

		$data .=<<<EOF

<div id="imCommentBox">
	<div id="comment-hint" style="">正在提交评论...</div>
	<input name="articleID" value="$aID" type="hidden" id="imArticleID" />
	<span>正在为您准备评论控件</span>
</div>

<div id="comment-startflag"></div>
<div id="comment-endflag"></div>

<script type="text/javascript" >
	mw.loader.using( 'ext.imComment', function() {});
</script>
EOF;

		return true;
	}

	public static function onUserLoginComplete( User &$user, &$inject_html ) {
		global $wgCommentSecret, $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgCookieExpiration;
		
		$userID = $user->mId;
		$userName = $user->getName();

		$login_code = substr(md5($userID.$wgCommentSecret.$userName),2,10).substr(md5($userName.$wgCommentSecret.$userID),2,10).substr(md5($wgCommentSecret.$userName.$userID),2,10);

		$mysql = new PDO('mysql:host=89.32.146.215;dbname=moecomment', 'moecomment', '67nPXqrcazHSAVVc');
		$mission = $mysql->prepare("INSERT INTO `moecomment`.`user_table` (`id`, `account_name`, `token`, `avatar_url`) VALUES (NULL, ?, ?, NULL) ON DUPLICATE KEY UPDATE `token` = ?;");
		if ($mission->execute(array(
				$userName,
				$login_code,
				$login_code,
		))) {
			$commentUserId = $mysql->lastInsertId();
			if ($commentUserId == 0) {
				$mission = $mysql->prepare("SELECT `id` FROM `user_table` WHERE `account_name` = ?;");
				$mission->execute(array($userName));
				$data = $mission->fetch(PDO::FETCH_OBJ);
				if (!empty($data)) {
					setcookie(	'comment_token', 
								$data->id.'|'.$login_code, 
								time() + $wgCookieExpiration, 
								$wgCookiePath, 
								$wgCookieDomain, 
								$wgCookieSecure, 
								false
							);
				}
			}else{		
				setcookie(	'comment_token', 
							$commentUserId.'|'.$login_code, 
							time() + $wgCookieExpiration, 
							$wgCookiePath, 
							$wgCookieDomain, 
							$wgCookieSecure, 
							false
						);
			}			
		}
	}

	public static function onUserLogoutComplete( User &$user, &$inject_html ) {
			global $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgCookieExpiration;

			setcookie(	'comment_token', 
						'', 
						time() - $wgCookieExpiration, 
						$wgCookiePath, 
						$wgCookieDomain, 
						$wgCookieSecure, 
						false
					);			
	}

}

