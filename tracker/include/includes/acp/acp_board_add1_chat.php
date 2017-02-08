<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_chat.php 1.000 2009-08-13 11:48:00 PPK $
* @copyright (c) 2009 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_chat');

$display_vars = array(
	'title'	=> 'ACP_CHAT_SETTINGS',
	'vars'	=> array(
		'legend1'				=> 'ACP_CHAT_SETTINGS',
		'ppkbb_chat_enable'		=> array('lang' => 'CHAT_ENABLE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_chat_display'		=> array('lang' => 'CHAT_DISPLAY', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_guests'		=> array('lang' => 'CHAT_GUESTS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_guests_option'		=> array('lang' => 'CHAT_GUESTS_OPTION', 'validate' => 'array', 'type' => 'array:16:16', 'method' => false, 'explain' => true,),
		'ppkbb_chat_murefresh'		=> array('lang' => 'CHAT_MUREFRESH', 'validate' => 'array', 'type' => 'array:2:2', 'method' => false, 'explain' => true,),
		'ppkbb_chat_inactive_time'		=> array('lang' => 'CHAT_INACTIVE_TIME', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_chat_messlength'		=> array('lang' => 'CHAT_MESSLENGTH', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_messdisplay'		=> array('lang' => 'CHAT_MESSDISPLAY', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_waittime'		=> array('lang' => 'CHAT_WAITTIME', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_chat_cleanup_interval'		=> array('lang' => 'CHAT_CLEANUP_INTERVAL', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_chat_killtime'		=> array('lang' => 'CHAT_KILLTIME', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_chat_marchive'		=> array('lang' => 'CHAT_MARCHIVE', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_chat_maxsmiles'		=> array('lang' => 'CHAT_MAXSMILES', 'validate' => 'string', 'type' => 'text:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_chat_autokill_onpost'		=> array('lang' => 'CHAT_AKILL_POST', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_autokill_onrefresh'		=> array('lang' => 'CHAT_AKILL_REFRESH', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_enable_pm'		=> array('lang' => 'CHAT_ENABLE_PM', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_enable_bbcodes'		=> array('lang' => 'CHAT_ENABLE_BBCODES', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_enable_magicurl'		=> array('lang' => 'CHAT_ENABLE_MAGICURL', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_enable_acommands'		=> array('lang' => 'CHAT_ENABLE_ACOMMANDS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		//'ppkbb_portal_chat'		=> array('lang' => 'CHAT_PORTAL_CHAT', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_index_chat'		=> array('lang' => 'CHAT_INDEX_CHAT', 'validate' => 'int:0', 'type' => 'custom', 'method' => 'select_chat_forums', 'explain' => true,),
		'ppkbb_index_chat2'		=> array('lang' => 'CHAT_INDEX_CHAT2', 'validate' => 'int:0', 'type' => 'custom', 'method' => 'select_chat_forums', 'explain' => true,),
		'ppkbb_chat_qbantime' => array('lang' => 'CHAT_QBANTIME', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_logs'		=> array('lang' => 'CHAT_LOGS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_height'		=> array('lang' => 'CHAT_HEIGHT', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_chat_bot'		=> array('lang' => 'CHAT_BOT', 'validate' => 'array', 'type' => 'array:16:16', 'method' => false, 'explain' => true,),
		'ppkbb_chat_botforums'		=> array('lang' => 'CHAT_BOTFORUMS', 'validate' => 'array', 'type' => 'custom', 'method' => 'select_tracker_forums', 'explain' => true,),
		'ppkbb_chat_botforums_trueexclude'		=> array('lang' => 'CHAT_BOTFORUMS_TRUEEXCLUDE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_chat_umclean'		=> array('lang' => 'CHAT_UMCLEAN', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_chat_avatars' => array('lang' => 'CHAT_AVATARS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true),
		'ppkbb_chat_sounds' => array('lang' => 'CHAT_SOUNDS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true),
	)
);
?>
