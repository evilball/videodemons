<?php
/**
*
* @package ppkBB3cker
* @version $Id: cacommchat.php 1.000 2010-03-14 13:17:00 PPK $
* @copyright (c) 2010 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if($acommand=='/m')
{
	$sql="DELETE FROM ".PPKCHAT_MESSAGES_TABLE." WHERE room='{$forum_id}'";
	$result=my_sql_query($sql);
	if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][2])
	{
		include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
		chat_add_log(0, $chat_user['user_id'], $forum_id, 0, 'LOG_CHAT_MCLEAN', '');
	}
}
else if($acommand=='/r' && $rights[16] && $config['ppkbb_chat_marchive'][0])
{
	$sql="SELECT id FROM ".PPKCHAT_MESSAGES_TABLE.' ORDER BY id DESC LIMIT '.($config['ppkbb_chat_messdisplay']-1).', 1';
	$result=my_sql_query($sql);
	$last_mess_id=mysql_fetch_array($result);
	mysql_free_result($result);
	$last_mess_id = isset($last_mess_id['id']) ? $last_mess_id['id'] : 0;
	if($last_mess_id)
	{
		$sql="DELETE FROM ".PPKCHAT_MESSAGES_TABLE." WHERE id < {$last_mess_id} AND room='{$forum_id}' AND date > ".($dt - ($config['ppkbb_chat_marchive'][0]));
		$result=my_sql_query($sql);
		if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][2])
		{
			include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
			chat_add_log(0, $chat_user['user_id'], $forum_id, 0, 'LOG_CHAT_ACLEAN', '');
		}
	}
}
else if($acommand=='/u')
{
	$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE room='{$forum_id}' AND user_id!='{$chat_user['user_id']}' AND lastaccess < {$dt}";
	$result=my_sql_query($sql);
	if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][2])
	{
		include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
		chat_add_log(0, $chat_user['user_id'], $forum_id, 0, 'LOG_CHAT_UCLEAN', '');
	}
}
else if($acommand=='/k')
{
	preg_match('#^/k (-?\d+)$#', $acomm, $match);
	if(isset($match[1]) && $match[1])
	{
		$match[1]=intval($match[1]);
		$sql="UPDATE ".PPKCHAT_USERS_TABLE." SET lastaccess='1' WHERE room='{$forum_id}' AND user_id='{$match[1]}' AND user_id!='{$chat_user['user_id']}' AND lastaccess < {$dt}";
		$result=my_sql_query($sql);
		if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][1])
		{
			$match[1] > 1 ? '' : $match[1]=1;
			include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
			chat_add_log(1, $chat_user['user_id'], $forum_id, $match[1], 'LOG_CHAT_UKICK', '');
		}
	}
}
else if($acommand=='/b')
{
	preg_match('#^/b (-?\d+) (\d+)$#', $acomm, $match);
	if(isset($match[1]) && $match[1] && isset($match[2]) && $match[2])
	{
		$match[1]=intval($match[1]);
		$match[2]=my_int_val($match[2]);
		$match[2] > 999 ? $match[2]=999 : '';
		$sql="UPDATE ".PPKCHAT_USERS_TABLE." SET lastaccess=lastaccess+".(3600 * $match[2])." WHERE room='{$forum_id}' AND user_id='{$match[1]}' AND user_id!='{$chat_user['user_id']}'";
		$result=my_sql_query($sql);
		if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][0])
		{
			$match[1] > 1 ? '' : $match[1]=1;
			include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
			chat_add_log(1, $chat_user['user_id'], $forum_id, $match[1], 'LOG_CHAT_UBAN', '');
		}
	}
}
else if($acommand=='/q')
{
	preg_match('#^/q (-?\d+)$#', $acomm, $match);
	if(isset($match[1]) && $match[1])
	{
		$match[1]=intval($match[1]);
		$sql="UPDATE ".PPKCHAT_USERS_TABLE." SET lastaccess=lastaccess+{$config['ppkbb_chat_qbantime']} WHERE room='{$forum_id}' AND user_id='{$match[1]}' AND user_id!='{$chat_user['user_id']}'";
		$result=my_sql_query($sql);
		if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][0])
		{
			$match[1] > 1 ? '' : $match[1]=1;
			include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
			chat_add_log(1, $chat_user['user_id'], $forum_id, $match[1], 'LOG_CHAT_UBAN', '');
		}
	}
}
else if($acommand=='/a')
{
	preg_match('#^/a (-?\d+)$#', $acomm, $match);
	if(isset($match[1]) && $match[1])
	{
		$match[1]=intval($match[1]);
		$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE room='{$forum_id}' AND user_id='{$match[1]}' AND user_id!='{$chat_user['user_id']}' AND lastaccess > {$dt}";
		$result=my_sql_query($sql);
		if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][0])
		{
			$match[1] > 1 ? '' : $match[1]=1;
			include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
			chat_add_log(1, $chat_user['user_id'], $forum_id, $match[1], 'LOG_CHAT_UUNBAN', '');
		}
	}
}
else
{
	$acommand='';
}
?>
