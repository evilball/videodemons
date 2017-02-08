<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_board_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_board',
			'title'		=> 'ACP_BOARD_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'		=> array('title' => 'ACP_BOARD_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'features'		=> array('title' => 'ACP_BOARD_FEATURES', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'avatar'		=> array('title' => 'ACP_AVATAR_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'message'		=> array('title' => 'ACP_MESSAGE_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION', 'ACP_MESSAGES')),
				'post'			=> array('title' => 'ACP_POST_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION', 'ACP_MESSAGES')),
				'signature'		=> array('title' => 'ACP_SIGNATURE_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'feed'			=> array('title' => 'ACP_FEED_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'registration'	=> array('title' => 'ACP_REGISTER_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),

				'auth'		=> array('title' => 'ACP_AUTH_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_CLIENT_COMMUNICATION')),
				'email'		=> array('title' => 'ACP_EMAIL_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_CLIENT_COMMUNICATION')),

				'cookie'	=> array('title' => 'ACP_COOKIE_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_SERVER_CONFIGURATION')),
				'server'	=> array('title' => 'ACP_SERVER_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_SERVER_CONFIGURATION')),
				'security'	=> array('title' => 'ACP_SECURITY_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_SERVER_CONFIGURATION')),
				'load'		=> array('title' => 'ACP_LOAD_SETTINGS', 'auth' => 'acl_a_server', 'cat' => array('ACP_SERVER_CONFIGURATION')),
				'chat'		=> array('title' => 'ACP_CHAT_SETTINGS', 'auth' => 'acl_a_ppkchat', 'cat' => array('ACP_TRACKER')),
				'tracker'		=> array('title' => 'ACP_TRACKER_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'imgset'		=> array('title' => 'ACP_IMGSET_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'rss'		=> array('title' => 'ACP_RSS_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'candc'		=> array('title' => 'ACP_CANDC_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'rtrack'		=> array('title' => 'ACP_RTRACK_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'guest'		=> array('title' => 'ACP_GUEST_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'trestricts'		=> array('title' => 'ACP_TRESTRICTS_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'addfields'		=> array('title' => 'ACP_ADDFIELDS_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'addfield'		=> array('title' => 'ACP_ADDFIELD_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'rtracker'		=> array('title' => 'ACP_RTRACKER_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'statuses'	=> array('title' => 'ACP_STATUSES_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'groupset'	=> array('title' => 'ACP_GROUPSET_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'acheat'		=> array('title' => 'ACP_ACHEAT_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'downloadlog'	=> array('title' => 'ACP_DOWNLOADLOG_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
				'losttorrents'	=> array('title' => 'ACP_LOSTTORRENTS_SETTINGS', 'auth' => 'acl_a_ppktracker', 'cat' => array('ACP_TRACKER')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
