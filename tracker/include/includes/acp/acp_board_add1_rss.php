<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_rss.php 1.000 2012-06-14 09:43:08 PPK $
* @copyright (c) 2012 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_rss');

$display_vars = array(
	'title'	=> 'ACP_PPKBB_RSS',
	'vars'	=> array(

		'legend8'				=> 'ACP_PPKBB_RSS',
		'ppkbb_feed_torrents'		=> array('lang'	=> 'ACP_FEED_TRTORRENTS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_comments'		=> array('lang'	=> 'ACP_FEED_TRCOMMENTS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_ftorrents'		=> array('lang'	=> 'ACP_FEED_TRFTORRENTS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_fcomments'		=> array('lang'	=> 'ACP_FEED_TRFCOMMENTS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_tcomments'		=> array('lang'	=> 'ACP_FEED_TRTCOMMENTS',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_enblist'		=> array('lang'	=> 'ACP_FEED_ENBLIST',		'validate' => 'array',	'type' => 'custom',	'explain' => true,  'method' => 'select_tracker_forums'),
		'ppkbb_feed_trueenblist'		=> array('lang'	=> 'ACP_FEED_TRUEENBLIST',		'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => true ),
		'ppkbb_feed_torrents_sort'	=> array('lang' => 'ACP_FEED_TORRSORT',	'validate' => 'int:0',	'type' => 'radio:yes_no',				'explain' => true),
		'ppkbb_feed_torrents_limit'	=> array('lang' => 'ACP_FEED_TORRLIMIT',	'validate' => 'int:0',	'type' => 'text:3:4',				'explain' => true),
		'ppkbb_feed_comments_limit'	=> array('lang' => 'ACP_FEED_COMMLIMIT',	'validate' => 'int:0',	'type' => 'text:3:4',				'explain' => true),
		'ppkbb_feed_torrtime'	=> array('lang' => 'ACP_FEED_TORRTIME',	'validate' => 'int:0',	'type' => 'text:2:2',				'explain' => true),
		'ppkbb_feed_commtime'	=> array('lang' => 'ACP_FEED_COMMTIME',	'validate' => 'int:0',	'type' => 'text:2:2',				'explain' => true),

	)
);
?>
