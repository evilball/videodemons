<?php
/**
*
* @package ppkBB3cker
* @version $Id: ppkbb3cker_chat.php 1.000 2010-01-14 19:39:00 PPK $
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

if(!defined('IN_PORTAL'))
{
	$chat_id=$config['ppkbb_portal_chat'];

	if (!$chat_id)
	{
		trigger_error('CHAT_INDEX_ERROR');
	}
}

$sql = "SELECT f.*
	FROM " . FORUMS_TABLE . " f
	WHERE f.forum_id = {$chat_id}";
$result = $db->sql_query($sql);
$chat_data = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$chat_data)
{
	trigger_error(defined('IN_PORTAL') ? 'CHAT_PORTAL_ERROR' : 'CHAT_INDEX_ERROR');
}

$forum_aschat=$chat_data['forum_type'] == FORUM_POST && $chat_data['forumas']==2 ? 1 : 0;

if(!$forum_aschat)
{
	trigger_error(defined('IN_PORTAL') ? 'CHAT_PORTAL_ERROR' : 'CHAT_INDEX_ERROR');
}

$chat_alt=$chat_data['forum_status'] == ITEM_LOCKED ? true : false;

$chat=request_var('chat', '');
$chat_error=request_var('r', 0);
$dt=time();

include($phpbb_root_path.'chat/chat.'.$phpEx);

?>
