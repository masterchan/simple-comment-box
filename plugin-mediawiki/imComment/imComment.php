<?php
$wgExtensionCredits['parserhook'][] = array (
  'path' => __FILE__,
  'name' => 'imComment',
  'author' => 'masterchan',
  'url' => 'https://github.com/masterchan',
  'description' => '向所有项目页面添加评论空间',
  'version' => 1.0,
  'license-name' => 'WTFPL'
);


$dir = dirname( __FILE__ );

$wgAutoloadClasses['imCommentHooks'] = $dir . '/imComment.hooks.php';

$wgResourceModules['ext.imComment'] = array(
    'scripts' => 'lib/imComment.js',
	'styles' => 'lib/imComment.css',
	'localBasePath' => $dir,
	'remoteExtPath' => 'imComment'
);

$wgHooks['SkinAfterContent'][] = 'imCommentHooks::onSkinAfterContent';
$wgHooks['UserLoginComplete'][] = 'imCommentHooks::onUserLoginComplete';
$wgHooks['UserLogoutComplete'][] = 'imCommentHooks::onUserLogoutComplete';

return true;


