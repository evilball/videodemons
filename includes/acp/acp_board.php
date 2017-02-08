<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* @todo add cron intervals to server settings? (database_gc, queue_interval, session_gc, search_gc, cache_gc, warnings_gc)
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_board
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $cache;

		$user->add_lang('acp/board');
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit']) || isset($_POST['allow_quick_reply_enable'])) ? true : false;

		$form_key = 'acp_board';
		add_form_key($form_key);

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_BOARD_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_BOARD_SETTINGS',
						'sitename'				=> array('lang' => 'SITE_NAME',				'validate' => 'string',	'type' => 'text:40:255', 'explain' => false),
						'site_desc'				=> array('lang' => 'SITE_DESC',				'validate' => 'string',	'type' => 'text:40:255', 'explain' => false),
						'board_disable'			=> array('lang' => 'DISABLE_BOARD',			'validate' => 'bool',	'type' => 'custom', 'method' => 'board_disable', 'explain' => true),
						'board_disable_msg'		=> false,
						'default_lang'			=> array('lang' => 'DEFAULT_LANGUAGE',		'validate' => 'lang',	'type' => 'select', 'function' => 'language_select', 'params' => array('{CONFIG_VALUE}'), 'explain' => false),
						'default_dateformat'	=> array('lang' => 'DEFAULT_DATE_FORMAT',	'validate' => 'string',	'type' => 'custom', 'method' => 'dateformat_select', 'explain' => true),
						'board_timezone'		=> array('lang' => 'SYSTEM_TIMEZONE',		'validate' => 'string',	'type' => 'select', 'function' => 'tz_select', 'params' => array('{CONFIG_VALUE}', 1), 'explain' => true),
						'board_dst'				=> array('lang' => 'SYSTEM_DST',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'default_style'			=> array('lang' => 'DEFAULT_STYLE',			'validate' => 'int',	'type' => 'select', 'function' => 'style_select', 'params' => array('{CONFIG_VALUE}', false), 'explain' => false),
						'override_user_style'	=> array('lang' => 'OVERRIDE_STYLE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

						'legend2'				=> 'WARNINGS',
						'warnings_expire_days'	=> array('lang' => 'WARNINGS_EXPIRE',		'validate' => 'int',	'type' => 'text:3:4', 'explain' => true, 'append' => ' ' . $user->lang['DAYS']),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'features':
				$display_vars = array(
					'title'	=> 'ACP_BOARD_FEATURES',
					'vars'	=> array(
						'legend1'				=> 'ACP_BOARD_FEATURES',
						'allow_privmsg'			=> array('lang' => 'BOARD_PM',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_topic_notify'	=> array('lang' => 'ALLOW_TOPIC_NOTIFY',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_forum_notify'	=> array('lang' => 'ALLOW_FORUM_NOTIFY',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_namechange'		=> array('lang' => 'ALLOW_NAME_CHANGE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_attachments'		=> array('lang' => 'ALLOW_ATTACHMENTS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_pm_attach'		=> array('lang' => 'ALLOW_PM_ATTACHMENTS',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_pm_report'		=> array('lang' => 'ALLOW_PM_REPORT',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_bbcode'			=> array('lang' => 'ALLOW_BBCODE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_smilies'			=> array('lang' => 'ALLOW_SMILIES',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig'				=> array('lang' => 'ALLOW_SIG',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_nocensors'		=> array('lang' => 'ALLOW_NO_CENSORS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_bookmarks'		=> array('lang' => 'ALLOW_BOOKMARKS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_birthdays'		=> array('lang' => 'ALLOW_BIRTHDAYS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_quick_reply'		=> array('lang' => 'ALLOW_QUICK_REPLY',		'validate' => 'bool',	'type' => 'custom', 'method' => 'quick_reply', 'explain' => true),
						'allow_quick_reply_smilies'	=> array('lang' => 'ALLOW_QUICK_REPLY_SMILIES',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_quick_reply_bbcode'		=> array('lang' => 'ALLOW_QUICK_REPLY_BBCODE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'legend2'				=> 'ACP_LOAD_SETTINGS',
						'load_birthdays'		=> array('lang' => 'YES_BIRTHDAYS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_moderators'		=> array('lang' => 'YES_MODERATORS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_jumpbox'			=> array('lang' => 'YES_JUMPBOX',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_cpf_memberlist'	=> array('lang' => 'LOAD_CPF_MEMBERLIST',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_cpf_viewprofile'	=> array('lang' => 'LOAD_CPF_VIEWPROFILE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_cpf_viewtopic'	=> array('lang' => 'LOAD_CPF_VIEWTOPIC',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'avatar':
				$display_vars = array(
					'title'	=> 'ACP_AVATAR_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_AVATAR_SETTINGS',

						'avatar_min_width'		=> array('lang' => 'MIN_AVATAR_SIZE', 'validate' => 'int:0', 'type' => false, 'method' => false, 'explain' => false,),
						'avatar_min_height'		=> array('lang' => 'MIN_AVATAR_SIZE', 'validate' => 'int:0', 'type' => false, 'method' => false, 'explain' => false,),
						'avatar_max_width'		=> array('lang' => 'MAX_AVATAR_SIZE', 'validate' => 'int:0', 'type' => false, 'method' => false, 'explain' => false,),
						'avatar_max_height'		=> array('lang' => 'MAX_AVATAR_SIZE', 'validate' => 'int:0', 'type' => false, 'method' => false, 'explain' => false,),

						'allow_avatar'			=> array('lang' => 'ALLOW_AVATARS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_avatar_local'	=> array('lang' => 'ALLOW_LOCAL',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_avatar_remote'	=> array('lang' => 'ALLOW_REMOTE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_avatar_upload'	=> array('lang' => 'ALLOW_UPLOAD',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_avatar_remote_upload'=> array('lang' => 'ALLOW_REMOTE_UPLOAD', 'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'avatar_filesize'		=> array('lang' => 'MAX_FILESIZE',			'validate' => 'int:0',	'type' => 'text:4:10', 'explain' => true, 'append' => ' ' . $user->lang['BYTES']),
						'avatar_min'			=> array('lang' => 'MIN_AVATAR_SIZE',		'validate' => 'int:0',	'type' => 'dimension:3:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
						'avatar_max'			=> array('lang' => 'MAX_AVATAR_SIZE',		'validate' => 'int:0',	'type' => 'dimension:3:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
						'avatar_path'			=> array('lang' => 'AVATAR_STORAGE_PATH',	'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true),
						'avatar_gallery_path'	=> array('lang' => 'AVATAR_GALLERY_PATH',	'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true)
					)
				);
			break;

			case 'chat':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_chat.{$phpEx}");
			break;

			case 'tracker':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_tracker.{$phpEx}");
			break;

			case 'candc':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_candc.{$phpEx}");
			break;

			case 'rss':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_rss.{$phpEx}");
			break;

			case 'rtrack':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_rtrack.{$phpEx}");
			break;

			case 'guest':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_guest.{$phpEx}");
			break;

			case 'imgset':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_imgset.{$phpEx}");
			break;

			case 'trestricts':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_trestricts.{$phpEx}");
			break;

			case 'addfields':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_addfields.{$phpEx}");
			break;

			case 'addfield':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_addfield.{$phpEx}");
			break;

			case 'rtracker':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_rtracker.{$phpEx}");
			break;

			case 'acheat':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_acheat.{$phpEx}");
			break;
			case 'statuses':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_statuses.{$phpEx}");
			break;

			case 'groupset':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_groupset.{$phpEx}");
			break;

			case 'downloadlog':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_downloadlog.{$phpEx}");
			break;

			case 'losttorrents':
				include_once("$phpbb_root_path/tracker/include/includes/acp/acp_board_add1_losttorrents.{$phpEx}");
			break;
			case 'message':
				$display_vars = array(
					'title'	=> 'ACP_MESSAGE_SETTINGS',
					'lang'	=> 'ucp',
					'vars'	=> array(
						'legend1'				=> 'GENERAL_SETTINGS',
						'allow_privmsg'			=> array('lang' => 'BOARD_PM',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'pm_max_boxes'			=> array('lang' => 'BOXES_MAX',				'validate' => 'int:0',	'type' => 'text:4:4', 'explain' => true),
						'pm_max_msgs'			=> array('lang' => 'BOXES_LIMIT',			'validate' => 'int:0',	'type' => 'text:4:4', 'explain' => true),
						'full_folder_action'	=> array('lang' => 'FULL_FOLDER_ACTION',	'validate' => 'int',	'type' => 'select', 'method' => 'full_folder_select', 'explain' => true),
						'pm_edit_time'			=> array('lang' => 'PM_EDIT_TIME',			'validate' => 'int:0',	'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['MINUTES']),
						'pm_max_recipients'		=> array('lang' => 'PM_MAX_RECIPIENTS',		'validate' => 'int:0',	'type' => 'text:5:5', 'explain' => true),

						'legend2'				=> 'GENERAL_OPTIONS',
						'allow_mass_pm'			=> array('lang' => 'ALLOW_MASS_PM',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'auth_bbcode_pm'		=> array('lang' => 'ALLOW_BBCODE_PM',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'auth_smilies_pm'		=> array('lang' => 'ALLOW_SMILIES_PM',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_pm_attach'		=> array('lang' => 'ALLOW_PM_ATTACHMENTS',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_pm'			=> array('lang' => 'ALLOW_SIG_PM',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'print_pm'				=> array('lang' => 'ALLOW_PRINT_PM',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'forward_pm'			=> array('lang' => 'ALLOW_FORWARD_PM',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'auth_img_pm'			=> array('lang' => 'ALLOW_IMG_PM',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'auth_flash_pm'			=> array('lang' => 'ALLOW_FLASH_PM',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'enable_pm_icons'		=> array('lang' => 'ENABLE_PM_ICONS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'post':
				$display_vars = array(
					'title'	=> 'ACP_POST_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'GENERAL_OPTIONS',
						'allow_topic_notify'	=> array('lang' => 'ALLOW_TOPIC_NOTIFY',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_forum_notify'	=> array('lang' => 'ALLOW_FORUM_NOTIFY',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_bbcode'			=> array('lang' => 'ALLOW_BBCODE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_post_flash'		=> array('lang' => 'ALLOW_POST_FLASH',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_smilies'			=> array('lang' => 'ALLOW_SMILIES',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_post_links'		=> array('lang' => 'ALLOW_POST_LINKS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_nocensors'		=> array('lang' => 'ALLOW_NO_CENSORS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_bookmarks'		=> array('lang' => 'ALLOW_BOOKMARKS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'enable_post_confirm'	=> array('lang' => 'VISUAL_CONFIRM_POST',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_quick_reply'		=> array('lang' => 'ALLOW_QUICK_REPLY',		'validate' => 'bool',	'type' => 'custom', 'method' => 'quick_reply', 'explain' => true),
						'allow_quick_reply_smilies'	=> array('lang' => 'ALLOW_QUICK_REPLY_SMILIES',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'allow_quick_reply_bbcode'		=> array('lang' => 'ALLOW_QUICK_REPLY_BBCODE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'legend2'				=> 'POSTING',
						'bump_type'				=> false,
						'edit_time'				=> array('lang' => 'EDIT_TIME',				'validate' => 'int:0',		'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['MINUTES']),
						'delete_time'			=> array('lang' => 'DELETE_TIME',			'validate' => 'int:0',		'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['MINUTES']),
						'display_last_edited'	=> array('lang' => 'DISPLAY_LAST_EDITED',	'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true),
						'flood_interval'		=> array('lang' => 'FLOOD_INTERVAL',		'validate' => 'int:0',		'type' => 'text:3:10', 'explain' => true, 'append' => ' ' . $user->lang['SECONDS']),
						'bump_interval'			=> array('lang' => 'BUMP_INTERVAL',			'validate' => 'int:0',		'type' => 'custom', 'method' => 'bump_interval', 'explain' => true),
						'topics_per_page'		=> array('lang' => 'TOPICS_PER_PAGE',		'validate' => 'int:1',		'type' => 'text:3:4', 'explain' => false),
						'posts_per_page'		=> array('lang' => 'POSTS_PER_PAGE',		'validate' => 'int:1',		'type' => 'text:3:4', 'explain' => false),
						'smilies_per_page'		=> array('lang' => 'SMILIES_PER_PAGE',		'validate' => 'int:1',		'type' => 'text:3:4', 'explain' => false),
						'hot_threshold'			=> array('lang' => 'HOT_THRESHOLD',			'validate' => 'int:0',		'type' => 'text:3:4', 'explain' => true),
						'max_poll_options'		=> array('lang' => 'MAX_POLL_OPTIONS',		'validate' => 'int:2:127',	'type' => 'text:4:4', 'explain' => false),
						'max_post_chars'		=> array('lang' => 'CHAR_LIMIT',			'validate' => 'int:0',		'type' => 'text:4:6', 'explain' => true),
						'min_post_chars'		=> array('lang' => 'MIN_CHAR_LIMIT',		'validate' => 'int:1',		'type' => 'text:4:6', 'explain' => true),
						'max_post_smilies'		=> array('lang' => 'SMILIES_LIMIT',			'validate' => 'int:0',		'type' => 'text:4:4', 'explain' => true),
						'max_post_urls'			=> array('lang' => 'MAX_POST_URLS',			'validate' => 'int:0',		'type' => 'text:5:4', 'explain' => true),
						'max_post_font_size'	=> array('lang' => 'MAX_POST_FONT_SIZE',	'validate' => 'int:0',		'type' => 'text:5:4', 'explain' => true, 'append' => ' %'),
						'max_quote_depth'		=> array('lang' => 'QUOTE_DEPTH_LIMIT',		'validate' => 'int:0',		'type' => 'text:4:4', 'explain' => true),
						'max_post_img_width'	=> array('lang' => 'MAX_POST_IMG_WIDTH',	'validate' => 'int:0',		'type' => 'text:5:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
						'max_post_img_height'	=> array('lang' => 'MAX_POST_IMG_HEIGHT',	'validate' => 'int:0',		'type' => 'text:5:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'signature':
				$display_vars = array(
					'title'	=> 'ACP_SIGNATURE_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'GENERAL_OPTIONS',
						'allow_sig'				=> array('lang' => 'ALLOW_SIG',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_bbcode'		=> array('lang' => 'ALLOW_SIG_BBCODE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_img'			=> array('lang' => 'ALLOW_SIG_IMG',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_flash'		=> array('lang' => 'ALLOW_SIG_FLASH',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_smilies'		=> array('lang' => 'ALLOW_SIG_SMILIES',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_sig_links'		=> array('lang' => 'ALLOW_SIG_LINKS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

						'legend2'				=> 'GENERAL_SETTINGS',
						'max_sig_chars'			=> array('lang' => 'MAX_SIG_LENGTH',		'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true),
						'max_sig_urls'			=> array('lang' => 'MAX_SIG_URLS',			'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true),
						'max_sig_font_size'		=> array('lang' => 'MAX_SIG_FONT_SIZE',		'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true, 'append' => ' %'),
						'max_sig_smilies'		=> array('lang' => 'MAX_SIG_SMILIES',		'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true),
						'max_sig_img_width'		=> array('lang' => 'MAX_SIG_IMG_WIDTH',		'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
						'max_sig_img_height'	=> array('lang' => 'MAX_SIG_IMG_HEIGHT',	'validate' => 'int:0',	'type' => 'text:5:4', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'registration':
				$display_vars = array(
					'title'	=> 'ACP_REGISTER_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'GENERAL_SETTINGS',
						'max_name_chars'		=> array('lang' => 'USERNAME_LENGTH', 'validate' => 'int:8:180', 'type' => false, 'method' => false, 'explain' => false,),
						'max_pass_chars'		=> array('lang' => 'PASSWORD_LENGTH', 'validate' => 'int:8:255', 'type' => false, 'method' => false, 'explain' => false,),

						'require_activation'	=> array('lang' => 'ACC_ACTIVATION',	'validate' => 'int',	'type' => 'select', 'method' => 'select_acc_activation', 'explain' => true),
						'new_member_post_limit'	=> array('lang' => 'NEW_MEMBER_POST_LIMIT', 'validate' => 'int:0:255', 'type' => 'text:4:4', 'explain' => true, 'append' => ' ' . $user->lang['POSTS']),
						'new_member_group_default'=> array('lang' => 'NEW_MEMBER_GROUP_DEFAULT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
						'min_name_chars'		=> array('lang' => 'USERNAME_LENGTH',	'validate' => 'int:1',	'type' => 'custom:5:180', 'method' => 'username_length', 'explain' => true),
						'min_pass_chars'		=> array('lang' => 'PASSWORD_LENGTH',	'validate' => 'int:1',	'type' => 'custom', 'method' => 'password_length', 'explain' => true),
						'allow_name_chars'		=> array('lang' => 'USERNAME_CHARS',	'validate' => 'string',	'type' => 'select', 'method' => 'select_username_chars', 'explain' => true),
						'pass_complex'			=> array('lang' => 'PASSWORD_TYPE',		'validate' => 'string',	'type' => 'select', 'method' => 'select_password_chars', 'explain' => true),
						'chg_passforce'			=> array('lang' => 'FORCE_PASS_CHANGE',	'validate' => 'int:0',	'type' => 'text:3:3', 'explain' => true, 'append' => ' ' . $user->lang['DAYS']),

						'legend2'				=> 'GENERAL_OPTIONS',
						'allow_namechange'		=> array('lang' => 'ALLOW_NAME_CHANGE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'allow_emailreuse'		=> array('lang' => 'ALLOW_EMAIL_REUSE',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'enable_confirm'		=> array('lang' => 'VISUAL_CONFIRM_REG',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'max_login_attempts'	=> array('lang' => 'MAX_LOGIN_ATTEMPTS',	'validate' => 'int:0',	'type' => 'text:3:3', 'explain' => true),
						'max_reg_attempts'		=> array('lang' => 'REG_LIMIT',				'validate' => 'int:0',	'type' => 'text:4:4', 'explain' => true),

						'legend3'			=> 'COPPA',
						'coppa_enable'		=> array('lang' => 'ENABLE_COPPA',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'coppa_mail'		=> array('lang' => 'COPPA_MAIL',		'validate' => 'string',	'type' => 'textarea:5:40', 'explain' => true),
						'coppa_fax'			=> array('lang' => 'COPPA_FAX',			'validate' => 'string',	'type' => 'text:25:100', 'explain' => false),

						'legend4'			=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'feed':
				$display_vars = array(
					'title'	=> 'ACP_FEED_MANAGEMENT',
					'vars'	=> array(
						'legend1'					=> 'ACP_FEED_GENERAL',
						'feed_enable'				=> array('lang' => 'ACP_FEED_ENABLE',				'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_item_statistics'		=> array('lang' => 'ACP_FEED_ITEM_STATISTICS',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),
						'feed_http_auth'			=> array('lang' => 'ACP_FEED_HTTP_AUTH',			'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true),

						'legend2'					=> 'ACP_FEED_POST_BASED',
						'feed_limit_post'			=> array('lang' => 'ACP_FEED_LIMIT',				'validate' => 'int:5',	'type' => 'text:3:4',				'explain' => true),
						'feed_overall'				=> array('lang' => 'ACP_FEED_OVERALL',				'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_forum'				=> array('lang' => 'ACP_FEED_FORUM',				'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_topic'				=> array('lang' => 'ACP_FEED_TOPIC',				'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),

						'legend3'					=> 'ACP_FEED_TOPIC_BASED',
						'feed_limit_topic'			=> array('lang' => 'ACP_FEED_LIMIT',				'validate' => 'int:5',	'type' => 'text:3:4',				'explain' => true),
						'feed_topics_new'			=> array('lang' => 'ACP_FEED_TOPICS_NEW',			'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_topics_active'		=> array('lang' => 'ACP_FEED_TOPICS_ACTIVE',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_news_id'				=> array('lang' => 'ACP_FEED_NEWS',					'validate' => 'string',	'type' => 'custom', 'method' => 'select_news_forums', 'explain' => true),

						'legend4'					=> 'ACP_FEED_SETTINGS_OTHER',
						'feed_overall_forums'		=> array('lang'	=> 'ACP_FEED_OVERALL_FORUMS',		'validate' => 'bool',	'type' => 'radio:enabled_disabled',	'explain' => true ),
						'feed_exclude_id'			=> array('lang' => 'ACP_FEED_EXCLUDE_ID',			'validate' => 'string',	'type' => 'custom', 'method' => 'select_exclude_forums', 'explain' => true),
					)
				);
			break;

			case 'cookie':
				$display_vars = array(
					'title'	=> 'ACP_COOKIE_SETTINGS',
					'vars'	=> array(
						'legend1'		=> 'ACP_COOKIE_SETTINGS',
						'cookie_domain'	=> array('lang' => 'COOKIE_DOMAIN',	'validate' => 'string',	'type' => 'text::255', 'explain' => false),
						'cookie_name'	=> array('lang' => 'COOKIE_NAME',	'validate' => 'string',	'type' => 'text::16', 'explain' => false),
						'cookie_path'	=> array('lang'	=> 'COOKIE_PATH',	'validate' => 'string',	'type' => 'text::255', 'explain' => false),
						'cookie_secure'	=> array('lang' => 'COOKIE_SECURE',	'validate' => 'bool',	'type' => 'radio:disabled_enabled', 'explain' => true)
					)
				);
			break;

			case 'load':
				$display_vars = array(
					'title'	=> 'ACP_LOAD_SETTINGS',
					'vars'	=> array(
						'legend1'			=> 'GENERAL_SETTINGS',
						'limit_load'		=> array('lang' => 'LIMIT_LOAD',		'validate' => 'string',	'type' => 'text:4:4', 'explain' => true),
						'session_length'	=> array('lang' => 'SESSION_LENGTH',	'validate' => 'int:60',	'type' => 'text:5:10', 'explain' => true, 'append' => ' ' . $user->lang['SECONDS']),
						'active_sessions'	=> array('lang' => 'LIMIT_SESSIONS',	'validate' => 'int:0',	'type' => 'text:4:4', 'explain' => true),
						'load_online_time'	=> array('lang' => 'ONLINE_LENGTH',		'validate' => 'int:0',	'type' => 'text:4:3', 'explain' => true, 'append' => ' ' . $user->lang['MINUTES']),

						'legend2'				=> 'GENERAL_OPTIONS',
						'load_db_track'			=> array('lang' => 'YES_POST_MARKING',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_db_lastread'		=> array('lang' => 'YES_READ_MARKING',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_anon_lastread'	=> array('lang' => 'YES_ANON_READ_MARKING',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_online'			=> array('lang' => 'YES_ONLINE',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_online_guests'	=> array('lang' => 'YES_ONLINE_GUESTS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_onlinetrack'		=> array('lang' => 'YES_ONLINE_TRACK',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_birthdays'		=> array('lang' => 'YES_BIRTHDAYS',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_unreads_search'	=> array('lang' => 'YES_UNREAD_SEARCH',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_moderators'		=> array('lang' => 'YES_MODERATORS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_jumpbox'			=> array('lang' => 'YES_JUMPBOX',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_user_activity'	=> array('lang' => 'LOAD_USER_ACTIVITY',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'load_tplcompile'		=> array('lang' => 'RECOMPILE_STYLES',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

						'legend3'				=> 'CUSTOM_PROFILE_FIELDS',
						'load_cpf_memberlist'	=> array('lang' => 'LOAD_CPF_MEMBERLIST',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_cpf_viewprofile'	=> array('lang' => 'LOAD_CPF_VIEWPROFILE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'load_cpf_viewtopic'	=> array('lang' => 'LOAD_CPF_VIEWTOPIC',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),

						'legend4'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'auth':
				$display_vars = array(
					'title'	=> 'ACP_AUTH_SETTINGS',
					'vars'	=> array(
						'legend1'		=> 'ACP_AUTH_SETTINGS',
						'auth_method'	=> array('lang' => 'AUTH_METHOD',	'validate' => 'string',	'type' => 'select', 'method' => 'select_auth_method', 'explain' => false)
					)
				);
			break;

			case 'server':
				$display_vars = array(
					'title'	=> 'ACP_SERVER_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_SERVER_SETTINGS',
						'gzip_compress'			=> array('lang' => 'ENABLE_GZIP',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

						'legend2'				=> 'PATH_SETTINGS',
						'smilies_path'			=> array('lang' => 'SMILIES_PATH',		'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true),
						'icons_path'			=> array('lang' => 'ICONS_PATH',		'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true),
						'upload_icons_path'		=> array('lang' => 'UPLOAD_ICONS_PATH',	'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true),
						'ranks_path'			=> array('lang' => 'RANKS_PATH',		'validate' => 'rpath',	'type' => 'text:20:255', 'explain' => true),

						'legend3'				=> 'SERVER_URL_SETTINGS',
						'force_server_vars'		=> array('lang' => 'FORCE_SERVER_VARS',	'validate' => 'bool',			'type' => 'radio:yes_no', 'explain' => true),
						'server_protocol'		=> array('lang' => 'SERVER_PROTOCOL',	'validate' => 'string',			'type' => 'text:10:10', 'explain' => true),
						'server_name'			=> array('lang' => 'SERVER_NAME',		'validate' => 'string',			'type' => 'text:40:255', 'explain' => true),
						'server_port'			=> array('lang' => 'SERVER_PORT',		'validate' => 'int:0',			'type' => 'text:5:5', 'explain' => true),
						'script_path'			=> array('lang' => 'SCRIPT_PATH',		'validate' => 'script_path',	'type' => 'text::255', 'explain' => true),

						'legend4'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			case 'security':
				$display_vars = array(
					'title'	=> 'ACP_SECURITY_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_SECURITY_SETTINGS',
						'allow_autologin'		=> array('lang' => 'ALLOW_AUTOLOGIN',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'max_autologin_time'	=> array('lang' => 'AUTOLOGIN_LENGTH',		'validate' => 'int:0',	'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['DAYS']),
						'ip_check'				=> array('lang' => 'IP_VALID',				'validate' => 'int',	'type' => 'custom', 'method' => 'select_ip_check', 'explain' => true),
						'browser_check'			=> array('lang' => 'BROWSER_VALID',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'forwarded_for_check'	=> array('lang' => 'FORWARDED_FOR_VALID',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'referer_validation'	=> array('lang' => 'REFERER_VALID',		'validate' => 'int:0:3','type' => 'custom', 'method' => 'select_ref_check', 'explain' => true),
						'check_dnsbl'			=> array('lang' => 'CHECK_DNSBL',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'email_check_mx'		=> array('lang' => 'EMAIL_CHECK_MX',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'max_pass_chars'		=> array('lang' => 'PASSWORD_LENGTH', 'validate' => 'int:8:255', 'type' => false, 'method' => false, 'explain' => false,),
						'min_pass_chars'		=> array('lang' => 'PASSWORD_LENGTH',	'validate' => 'int:1',	'type' => 'custom', 'method' => 'password_length', 'explain' => true),
						'pass_complex'			=> array('lang' => 'PASSWORD_TYPE',			'validate' => 'string',	'type' => 'select', 'method' => 'select_password_chars', 'explain' => true),
						'chg_passforce'			=> array('lang' => 'FORCE_PASS_CHANGE',		'validate' => 'int:0',	'type' => 'text:3:3', 'explain' => true, 'append' => ' ' . $user->lang['DAYS']),
						'max_login_attempts'	=> array('lang' => 'MAX_LOGIN_ATTEMPTS',	'validate' => 'int:0',	'type' => 'text:3:3', 'explain' => true),
						'ip_login_limit_max'	=> array('lang' => 'IP_LOGIN_LIMIT_MAX',	'validate' => 'int:0',	'type' => 'text:3:3', 'explain' => true),
						'ip_login_limit_time'	=> array('lang' => 'IP_LOGIN_LIMIT_TIME',	'validate' => 'int:0',	'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['SECONDS']),
						'ip_login_limit_use_forwarded'	=> array('lang' => 'IP_LOGIN_LIMIT_USE_FORWARDED',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'tpl_allow_php'			=> array('lang' => 'TPL_ALLOW_PHP',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'form_token_lifetime'	=> array('lang' => 'FORM_TIME_MAX',			'validate' => 'int:-1',	'type' => 'text:5:5', 'explain' => true, 'append' => ' ' . $user->lang['SECONDS']),
						'form_token_sid_guests'	=> array('lang' => 'FORM_SID_GUESTS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

					)
				);
			break;

			case 'email':
				$display_vars = array(
					'title'	=> 'ACP_EMAIL_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'GENERAL_SETTINGS',
						'email_enable'			=> array('lang' => 'ENABLE_EMAIL',			'validate' => 'bool',	'type' => 'radio:enabled_disabled', 'explain' => true),
						'board_email_form'		=> array('lang' => 'BOARD_EMAIL_FORM',		'validate' => 'bool',	'type' => 'radio:enabled_disabled', 'explain' => true),
						'email_function_name'	=> array('lang' => 'EMAIL_FUNCTION_NAME',	'validate' => 'string',	'type' => 'text:20:50', 'explain' => true),
						'email_package_size'	=> array('lang' => 'EMAIL_PACKAGE_SIZE',	'validate' => 'int:0',	'type' => 'text:5:5', 'explain' => true),
						'board_contact'			=> array('lang' => 'CONTACT_EMAIL',			'validate' => 'email',	'type' => 'text:25:100', 'explain' => true),
						'board_email'			=> array('lang' => 'ADMIN_EMAIL',			'validate' => 'email',	'type' => 'text:25:100', 'explain' => true),
						'board_email_sig'		=> array('lang' => 'EMAIL_SIG',				'validate' => 'string',	'type' => 'textarea:5:30', 'explain' => true),
						'board_hide_emails'		=> array('lang' => 'BOARD_HIDE_EMAILS',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

						'legend2'				=> 'SMTP_SETTINGS',
						'smtp_delivery'			=> array('lang' => 'USE_SMTP',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'smtp_host'				=> array('lang' => 'SMTP_SERVER',			'validate' => 'string',	'type' => 'text:25:50', 'explain' => false),
						'smtp_port'				=> array('lang' => 'SMTP_PORT',				'validate' => 'int:0',	'type' => 'text:4:5', 'explain' => true),
						'smtp_auth_method'		=> array('lang' => 'SMTP_AUTH_METHOD',		'validate' => 'string',	'type' => 'select', 'method' => 'mail_auth_select', 'explain' => true),
						'smtp_username'			=> array('lang' => 'SMTP_USERNAME',			'validate' => 'string',	'type' => 'text:25:255', 'explain' => true),
						'smtp_password'			=> array('lang' => 'SMTP_PASSWORD',			'validate' => 'string',	'type' => 'password:25:255', 'explain' => true),

						'legend3'					=> 'ACP_SUBMIT_CHANGES',
					)
				);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		$dt=time();
		$tracker_mode=array('guest', 'rtrack', 'candc', 'rss', 'imgset', 'tracker', 'trestricts');
		$tracker_arrays_value=array('ppkbb_feed_enblist', 'ppkbb_extposters_exclude', 'ppkbb_extscreenshots_exclude', 'ppkbb_topdown_torrents_exclude', 'ppkbb_index_chat', 'ppkbb_index_chat2', 'ppkbb_chat_botforums');
		include("{$phpbb_root_path}tracker/include/config_map.{$phpEx}");

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{

			if(in_array($mode, $tracker_mode) || $mode=='chat')
			{
				if($submit)
				{
					if($mode=='rtrack')
					{
						$cache->destroy('_ppkbb3cker_cron_config');
					}
					if(isset($config_map[$config_name]) && $config_map[$config_name][0])
					{
						$config_value = $config_map[$config_name][0] > 1 ? request_var($config_name, array(0 => '')) : request_var($config_name, '');
						if(isset($config_map[$config_name]['field']))
						{
							$config_valuev = $config_map[$config_name][0] > 1 ? request_var($config_name.'v', array(0 => '')) : request_var($config_name.'v', '');
							foreach($config_map[$config_name]['field'] as $k=>$v)
							{
								$field_type=explode(':', $v);
								if($field_type[0]=='bytes' || $field_type[0]=='speed')
								{
									if($config_map[$config_name][0] > 1)
									{
										$config_value[$k]=get_size_value($config_valuev[$k], $config_value[$k]);
										strlen($config_value[$k]) > 20 ? $config_value[$k]=substr($config_value[$k], 0, 20) : '';
									}
									else
									{
										$config_value=get_size_value($config_valuev, $config_value);
										strlen($config_value) > 20 ? $config_value=substr($config_value, 0, 20) : '';
									}
								}
								else if($field_type[0]=='time')
								{
									if($config_map[$config_name][0] > 1)
									{
										$config_value[$k]=get_time_value($config_valuev[$k], $config_value[$k]);
										strlen($config_value[$k]) > 8 ? $config_value[$k]=substr($config_value[$k], 0, 8) : '';
									}
									else
									{
										$config_value=get_time_value($config_valuev, $config_value);
										strlen($config_value) > 8 ? $config_value=substr($config_value, 0, 8) : '';
									}
								}
							}
						}
						$config_map[$config_name][0] > 1 ? $config_value = implode(($config_map[$config_name][2] ? $config_map[$config_name][2] : ' '), $config_value) : '';
						$cfg_array[$config_name]=$config_value;
					}
					else if(in_array($config_name, $tracker_arrays_value))
					{
						$config_value = request_var($config_name, array(0 => ''));
						$config_value = implode(',', $config_value);
						$cfg_array[$config_name]=$config_value;
					}
					else if($config_name=='ppkbb_announce_url' && $cfg_array[$config_name]=='')
					{
						$cfg_array[$config_name]='/tracker/announce.'.$phpEx;
						//set_tracker_config('ppkbb_announce_url', $cfg_array[$config_name]);
					}
					else if($config_name=='ppkbb_subject_textlength' && intval($cfg_array[$config_name])>250)
					{
						$cfg_array[$config_name]=250;
					}
				}
			}
			if($mode=='chat')
			{
				if($submit)
				{
					$cache->destroy('_ppkbb3cker_chat_config');
					if(in_array($config_name, $tracker_arrays_value))
					{
						$config_value = request_var($config_name, array(0 => ''));
						$config_value = implode(',', $config_value);
						$cfg_array[$config_name]=$config_value;
					}
				}
			}
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			if ($config_name == 'auth_method' || $config_name == 'feed_news_id' || $config_name == 'feed_exclude_id')
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if(in_array($mode, $tracker_mode))
			{
				$mode=='guest' ? $user->lang['TRACKER_GUNREGTORR_SESSID_EXPLAIN']=sprintf($user->lang['TRACKER_GUNREGTORR_SESSID_EXPLAIN'], generate_board_url()."/tracker/announce2.{$phpEx}".(!empty($config['ppkbb_gtcunregtorr_sessid']) ? "?passkey={$config['ppkbb_gtcunregtorr_sessid']}" : '')) : '';
				if($submit)
				{
					if($config_name=='ppkbb_tcminannounce_interval')
					{
						$ppkbb_tcminannounce_interval = $this->new_config[$config_name];
						if($ppkbb_tcminannounce_interval && $ppkbb_tcminannounce_interval > $this->new_config['ppkbb_tcannounce_interval'])
						{
							$config_value=$this->new_config['ppkbb_tcminannounce_interval']=$this->new_config['ppkbb_tcannounce_interval'];
						}
					}
					else if($config_name=='ppkbb_tcdead_time')
					{
						$ppkbb_tcdead_time = $this->new_config[$config_name];
						if($ppkbb_tcdead_time <= $this->new_config['ppkbb_tcannounce_interval'])
						{
							$config_value=$this->new_config['ppkbb_tcdead_time']=intval($this->new_config['ppkbb_tcannounce_interval']*1.25);
						}
					}
					else if($config_name=='ppkbb_tccleanup_interval')
					{
						$ppkbb_tccleanup_interval = $this->new_config[$config_name];
						if($ppkbb_tccleanup_interval <= $this->new_config['ppkbb_tcannounce_interval'])
						{
							$config_value=$this->new_config['ppkbb_tccleanup_interval']=intval($this->new_config['ppkbb_tcannounce_interval']*1.5);
						}
					}
					else if($config_name=='ppkbb_hicons_fields')
					{
						$ppkbb_hicons_fields = $this->new_config[$config_name];
						if($ppkbb_hicons_fields!='' && !preg_match('/^https?:\/\/(\w+|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $ppkbb_hicons_fields))
						{
							$config_value=$this->new_config['ppkbb_hicons_fields']='';
						}
					}
					else if($config_name=='ppkbb_subforumslist')
					{
						$ppkbb_subforumslist = $this->new_config[$config_name];
						if($ppkbb_subforumslist!='')
						{
							$sql="UPDATE ".FORUMS_TABLE." SET forum_subforumslist_type='".$db->sql_escape($ppkbb_subforumslist)."'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_subforumslist']='';
						}
					}
					else if($config_name=='ppkbb_fpep_enable')
					{
						$ppkbb_fpep_enable = $this->new_config[$config_name];
						if($ppkbb_fpep_enable!='')
						{
							$sql="UPDATE ".FORUMS_TABLE." SET forum_first_post_show='".$db->sql_escape($ppkbb_fpep_enable)."'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_fpep_enable']='';
						}
					}
					else if($config_name=='ppkbb_forums_qr')
					{
						$ppkbb_forums_qr = $this->new_config[$config_name];
						if($ppkbb_forums_qr)
						{
							$sql="UPDATE ".FORUMS_TABLE." SET forum_flags=forum_flags ".($ppkbb_forums_qr==1 ? '+' : '-')." 64 WHERE forum_flags & 64".($ppkbb_forums_qr==1 ? '!=' : '=').' 64';
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_forums_qr']=0;
						}
					}
					else if($config_name=='ppkbb_recount_finished')
					{
						$ppkbb_recount_finished = $this->new_config[$config_name];
						if($ppkbb_recount_finished)
						{
							$result=$db->sql_query("UPDATE ".TRACKER_SNATCHED_TABLE." SET finished='1' WHERE to_go='0'");
							$result2=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET times_completed='0'");
							$result3=$db->sql_query("SELECT torrent, COUNT(*) finished FROM ".TRACKER_SNATCHED_TABLE." WHERE finished='1' GROUP BY torrent");
							while($row=$db->sql_fetchrow($result3))
							{
								$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET times_completed='{$row['finished']}' WHERE id='{$row['torrent']}'";
								$db->sql_query($sql);
							}
							$db->sql_freeresult($result3);
							$config_value=$this->new_config['ppkbb_recount_finished']=0;
						}
					}
					else if($config_name=='ppkbb_recount_thanks')
					{
						$ppkbb_recount_thanks = $this->new_config[$config_name];
						if($ppkbb_recount_thanks)
						{
							$result=$db->sql_query("SELECT COUNT(user_id) from_user_thanks, user_id FROM `".TRACKER_THANKS_TABLE."` GROUP BY user_id");
							while($row=$db->sql_fetchrow($result))
							{
								$db->sql_query("UPDATE `".USERS_TABLE."` SET user_fromthanks_count='{$row['from_user_thanks']}' WHERE user_id='{$row['user_id']}'");
							}
							$db->sql_freeresult($result);

							$result=$db->sql_query("SELECT COUNT(to_user) to_user_thanks, to_user user_id FROM `".TRACKER_THANKS_TABLE."` GROUP BY to_user");
							while($row=$db->sql_fetchrow($result))
							{
								$db->sql_query("UPDATE `".USERS_TABLE."` SET user_tothanks_count='{$row['to_user_thanks']}' WHERE user_id='{$row['user_id']}'");
							}
							$db->sql_freeresult($result);
							$config_value=$this->new_config['ppkbb_recount_thanks']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_unregtorr')
					{
						$ppkbb_trclear_unregtorr = $this->new_config[$config_name];
						if($ppkbb_trclear_unregtorr==1 || $ppkbb_trclear_unregtorr==3)
						{
							$sql="DELETE FROM ".ATTACHMENTS_TABLE." WHERE attach_id IN(SELECT id FROM ".TRACKER_TORRENTS_TABLE." WHERE unreg='1' AND poster_id!='1')";
							$db->sql_query($sql);
							$sql="DELETE FROM ".TRACKER_TORRENTS_TABLE." WHERE unreg='1' AND poster_id!='1'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_unregtorr']=0;
						}
						if($ppkbb_trclear_unregtorr==2 || $ppkbb_trclear_unregtorr==3)
						{
							$sql="DELETE FROM ".ATTACHMENTS_TABLE." WHERE attach_id IN(SELECT id FROM ".TRACKER_TORRENTS_TABLE." WHERE unreg='1' AND poster_id='1')";
							$db->sql_query($sql);
							$sql="DELETE FROM ".TRACKER_TORRENTS_TABLE." WHERE unreg='1' AND poster_id='1'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_unregtorr']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_rannounces')
					{
						$ppkbb_trclear_rannounces = $this->new_config[$config_name];
						if($ppkbb_trclear_rannounces)
						{
							$sql="TRUNCATE TABLE ".TRACKER_RANNOUNCES_TABLE."";
							$db->sql_query($sql);
							$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET rem_seeders='0', rem_leechers='0', rem_times_completed='0', lastremote='0'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_rannounces']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_trtrack')
					{
						$ppkbb_trclear_trtrack = $this->new_config[$config_name];
						if($ppkbb_trclear_trtrack)
						{
							$sql="DELETE FROM ".TRACKER_RTRACK_TABLE." WHERE torrent!='0'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_trtrack']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_urtrack')
					{
						$ppkbb_trclear_urtrack = $this->new_config[$config_name];
						if($ppkbb_trclear_urtrack)
						{
							$sql="DELETE FROM ".TRACKER_RTRACK_TABLE." WHERE rtrack_user!='0'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_urtrack']=0;
						}
					}
					else if($config_name=='ppkbb_trclean_sticky')
					{
						$ppkbb_trclean_sticky = $this->new_config[$config_name];
						if($ppkbb_trclean_sticky)
						{
							$result=$db->sql_query("SELECT t.topic_id FROM ".TOPICS_TABLE." t, ".TRACKER_TORRENTS_TABLE." tt, ".FORUMS_TABLE." f WHERE t.topic_type='".POST_STICKY."' AND t.topic_id=tt.topic_id AND t.forum_id=f.forum_id AND f.forumas='1' AND t.forum_id!='0'");
							while($row=$db->sql_fetchrow($result))
							{
								$sql="UPDATE ".TOPICS_TABLE." SET topic_type='".POST_NORMAL."' WHERE topic_id='{$row['topic_id']}'";
								$db->sql_query($sql);
							}
							$db->sql_freeresult($result);
							$config_value=$this->new_config['ppkbb_trclean_sticky']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_pollres')
					{
						$ppkbb_trclear_pollres = $this->new_config[$config_name];
						if($ppkbb_trclear_pollres)
						{
							$result=$db->sql_query("SELECT t.topic_id FROM ".TOPICS_TABLE." t, ".POLL_OPTIONS_TABLE." po, ".FORUMS_TABLE." f WHERE t.topic_id=po.topic_id AND t.forum_id=f.forum_id AND f.forumas='1' AND t.forum_id!='0'");
							while($row=$db->sql_fetchrow($result))
							{
								$db->sql_query("DELETE FROM ".POLL_VOTES_TABLE." WHERE topic_id='{$row['topic_id']}'");
								$sql="UPDATE ".POLL_OPTIONS_TABLE." SET poll_option_total='0' WHERE topic_id='{$row['topic_id']}'";
								$db->sql_query($sql);
							}
							$db->sql_freeresult($result);
							$config_value=$this->new_config['ppkbb_trclear_pollres']=0;
						}
					}
					else if($config_name=='ppkbb_tcguests_enabled')
					{
						$ppkbb_tcguests_enabled_new = my_split_config($this->new_config[$config_name], $config_map[$config_name][0], $config_map[$config_name][1], $config_map[$config_name][2]);
						if($ppkbb_tcguests_enabled_new[0]!=$config['ppkbb_tcguests_enabled'][0])
						{
							$db->sql_query("DELETE FROM ".TRACKER_PEERS_TABLE." WHERE guests='1'");
							$db->sql_query("DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE guests='1'");
						}
					}
					else if($config_name=='ppkbb_peerstable_memory')
					{
						$ppkbb_peerstable_memory = $this->new_config[$config_name];
						$ppkbb_peerstable_memory2 = $config['ppkbb_peerstable_memory'];
						if($ppkbb_peerstable_memory2!=$ppkbb_peerstable_memory)
						{
							$ppkbb_peerstable_memory ? $db->sql_query("ALTER TABLE `".TRACKER_PEERS_TABLE."`  ENGINE = MEMORY") : $db->sql_query("ALTER TABLE `".TRACKER_PEERS_TABLE."`  ENGINE = MYISAM");
						}
					}
					else if($config_name=='ppkbb_trclean_polls')
					{
						$ppkbb_trclean_polls = $this->new_config[$config_name];
						if($ppkbb_trclean_polls)
						{
							$result=$db->sql_query("SELECT t.topic_id FROM ".TOPICS_TABLE." t, ".POLL_OPTIONS_TABLE." po, ".FORUMS_TABLE." f WHERE t.topic_id=po.topic_id AND t.forum_id=f.forum_id AND f.forumas='1' AND t.forum_id!='0'");
							while($row=$db->sql_fetchrow($result))
							{
								$db->sql_query("DELETE FROM ".POLL_OPTIONS_TABLE." WHERE topic_id='{$row['topic_id']}'");
								$db->sql_query("DELETE FROM ".POLL_VOTES_TABLE." WHERE topic_id='{$row['topic_id']}'");
								$sql="UPDATE ".TOPICS_TABLE." SET poll_title='', poll_start='0', poll_length='0', poll_max_options='0', poll_last_vote='0', poll_vote_change='0' WHERE topic_id='{$row['topic_id']}'";
								$db->sql_query($sql);
							}
							$db->sql_freeresult($result);
							$config_value=$this->new_config['ppkbb_trclean_polls']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_files')
					{
						$ppkbb_trclear_files = $this->new_config[$config_name];
						if($ppkbb_trclear_files)
						{
							$sql="TRUNCATE TABLE ".TRACKER_FILES_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_files']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_thanks')
					{
						$ppkbb_trclear_thanks = $this->new_config[$config_name];
						if($ppkbb_trclear_thanks)
						{
							$sql="TRUNCATE TABLE ".TRACKER_THANKS_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_thanks']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_snatched')
					{
						$ppkbb_trclear_snatched = $this->new_config[$config_name];
						if($ppkbb_trclear_snatched==1)
						{
							$sql="TRUNCATE TABLE ".TRACKER_SNATCHED_TABLE."";
							$db->sql_query($sql);
						}
						else if($ppkbb_trclear_snatched==2)
						{
							$sql="DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE uploaded='0' AND downloaded='0'";
							$db->sql_query($sql);
						}
						else if($ppkbb_trclear_snatched==3)
						{
							$sql="DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE uploaded!='0' OR downloaded!='0'";
							$db->sql_query($sql);
						}
						$config_value=$this->new_config['ppkbb_trclear_snatched']='';
					}
					else if($config_name=='ppkbb_trclear_torrents')
					{
						$ppkbb_trclear_torrents = $this->new_config[$config_name];
						if($ppkbb_trclear_torrents)
						{
							$sql="TRUNCATE TABLE ".TRACKER_TORRENTS_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_torrents']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_cronjobs')
					{
						$ppkbb_trclear_cronjobs = $this->new_config[$config_name];
						if($ppkbb_trclear_cronjobs)
						{
							$sql="TRUNCATE TABLE ".TRACKER_CRON_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_cronjobs']=0;
						}
					}
					else if($config_name=='ppkbb_trclear_guestsess')
					{
						$ppkbb_trclear_guestsess = $this->new_config[$config_name];
						if($ppkbb_trclear_guestsess)
						{
							$sql="TRUNCATE TABLE ".TRACKER_GUESTS_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_trclear_guestsess']=0;
						}
					}
					else if($config_name=='ppkbb_fix_statuses')
					{
						$ppkbb_fix_statuses = $this->new_config[$config_name];
						if($ppkbb_fix_statuses)
						{
							$torrent_statuses=get_torrent_statuses();
							$valid_statuses=array();
							if(sizeof($torrent_statuses['TRACKER_FORB_MARK']))
							{
								foreach($torrent_statuses['TRACKER_FORB_MARK'] as $k => $v)
								{
									$k=intval($k);
									$valid_statuses[$k]=$k;
								}
							}
							if(sizeof($valid_statuses))
							{
								$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='0' WHERE forb NOT IN('".implode("', '", $valid_statuses)."')";
								$db->sql_query($sql);
							}
							$config_value=$this->new_config['ppkbb_reset_ratio']=0;
						}
					}
					else if($config_name=='ppkbb_reset_ratio')
					{
						$ppkbb_reset_ratio = $this->new_config[$config_name];
						if($ppkbb_reset_ratio)
						{
							$sql="UPDATE ".USERS_TABLE." SET user_uploaded='0', user_downloaded='0', user_uploaded_self='0'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_reset_ratio']=0;
						}
					}
					else if($config_name=='ppkbb_reset_bonus')
					{
						$ppkbb_reset_bonus = $this->new_config[$config_name];
						if($ppkbb_reset_bonus)
						{
							$sql="UPDATE ".USERS_TABLE." SET user_bonus='0.000'";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_reset_bonus']=0;
						}
					}
					else if($config_name=='ppkbb_unset_tcache')
					{
						$ppkbb_unset_tcache = $this->new_config[$config_name];
						if($ppkbb_unset_tcache)
						{
							$sql="UPDATE ".TRACKER_PEERS_TABLE." SET rights=''";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_unset_tcache']=0;
						}
					}
					else if($config_name=='ppkbb_clear_peers')
					{
						$ppkbb_clear_peers = $this->new_config[$config_name];

						if($ppkbb_clear_peers=='all')
						{
							$sql="TRUNCATE TABLE ".TRACKER_PEERS_TABLE."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_clear_peers']=0;
						}
						else if($ppkbb_clear_peers=='time')
						{
							$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE last_action < ".($dt-$config['ppkbb_tcdead_time'])."";
							$db->sql_query($sql);
							$config_value=$this->new_config['ppkbb_clear_peers']=0;
						}
					}
					else if($config_name=='ppkbb_deadtorrents_autodelete')
					{
						$ppkbb_deadtorrents_autodelete=my_split_config($this->new_config[$config_name], $config_map[$config_name][0], $config_map[$config_name][1], $config_map[$config_name][2]);
						if($ppkbb_deadtorrents_autodelete[3])
						{
							$sql="SELECT forumas FROM ".FORUMS_TABLE." WHERE forum_id='{$ppkbb_deadtorrents_autodelete[3]}' AND forumas=1";
							$result=$db->sql_query($sql);
							if(!$db->sql_fetchrow($result))
							{
								$ppkbb_deadtorrents_autodelete[3]=0;
								$config_value=$this->new_config['ppkbb_deadtorrents_autodelete']=implode(' ', $ppkbb_deadtorrents_autodelete);
							}
							$db->sql_freeresult($result);
						}
					}
					else if($config_name=='ppkbb_gtcunregtorr_sessid')
					{
						$ppkbb_gtcunregtorr_sessid=$this->new_config[$config_name];
						if(!empty($ppkbb_gtcunregtorr_sessid) && (strlen($ppkbb_gtcunregtorr_sessid)!=32 || !preg_match('/[A-Za-z0-9]{32}/', $ppkbb_gtcunregtorr_sessid)))
						{
							$ppkbb_gtcunregtorr_sessid=strtolower(gen_rand_string(8).gen_rand_string(8).gen_rand_string(8).gen_rand_string(8));
							$config_value=$this->new_config['ppkbb_gtcunregtorr_sessid']=$ppkbb_gtcunregtorr_sessid;
						}
					}
					else if($config_name=='ppkbb_clean_snatch')
					{
						$ppkbb_clean_snatch = $this->new_config[$config_name];

						if($ppkbb_clean_snatch)
						{
							$sql="SELECT tt.id FROM ".TRACKER_TORRENTS_TABLE." tt LEFT JOIN ".TOPICS_TABLE." t ON (tt.poster_id=t.topic_poster AND tt.post_msg_id=t.topic_first_post_id) WHERE ISNULL(t.topic_first_post_id) AND tt.unreg='0'";
							$result=$db->sql_query($sql);
							$t_clean=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if(sizeof($t_clean))
							{
								$t_clean=implode("', '", $t_clean);

								$sql="DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE torrent IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE torrent IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_TORRENTS_TABLE." WHERE id IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_THANKS_TABLE." WHERE torrent_id IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_FILES_TABLE." WHERE id IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_RANNOUNCES_TABLE." WHERE torrent IN('{$t_clean}')";
								$db->sql_query($sql);

								$sql="DELETE FROM ".TRACKER_RTRACK_TABLE." WHERE torrent!='0' AND torrent IN('{$t_clean}')";
								$db->sql_query($sql);
							}

							$sql="SELECT s.id FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (s.torrent=t.id) WHERE ISNULL(t.id)";
							$result=$db->sql_query($sql);
							$t_clean2=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean2[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if(sizeof($t_clean2))
							{
								$t_clean2=implode("', '", $t_clean2);

								$sql="DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE id IN('{$t_clean2}')";
								$db->sql_query($sql);
							}

							$sql="SELECT s.id FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".USERS_TABLE." u ON (s.userid=u.user_id) WHERE ISNULL(u.user_id) AND s.guests='0'";
							$result=$db->sql_query($sql);
							$t_clean3=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean3[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if(sizeof($t_clean3))
							{
								$t_clean3=implode("', '", $t_clean3);

								$sql="DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE id IN('{$t_clean3}')";
								$db->sql_query($sql);
							}

							$sql="SELECT p.id FROM ".TRACKER_PEERS_TABLE." p LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (p.torrent=t.id) WHERE ISNULL(t.id)";
							$result=$db->sql_query($sql);
							$t_clean4=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean4[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if(sizeof($t_clean4))
							{
								$t_clean4=implode("', '", $t_clean4);

								$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE id IN('{$t_clean4}')";
								$db->sql_query($sql);
							}

							$sql="SELECT t.id FROM ".TRACKER_THANKS_TABLE." t LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (t.post_id=tt.post_msg_id) WHERE ISNULL(tt.id)";
							$result=$db->sql_query($sql);
							$t_clean5=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean5[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if($t_clean5)
							{
								$t_clean5=implode("', '", $t_clean5);

								$sql="DELETE FROM ".TRACKER_THANKS_TABLE." WHERE id IN('{$t_clean5}')";
								$db->sql_query($sql);
							}
							$db->sql_freeresult($result);

							$sql="SELECT f.id FROM ".TRACKER_FILES_TABLE." f LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (f.id=tt.id) WHERE ISNULL(tt.id)";
							$result=$db->sql_query($sql);
							$t_clean6=array();
							while($row=$db->sql_fetchrow($result))
							{
								$t_clean6[]=$row['id'];
							}
							$db->sql_freeresult($result);

							if(sizeof($t_clean6))
							{
								$t_clean6=implode("', '", $t_clean6);

								$sql="DELETE FROM ".TRACKER_FILES_TABLE." WHERE id IN('{$t_clean6}')";
								$db->sql_query($sql);
							}

							$config_value=$this->new_config['ppkbb_clean_snatch']=0;
						}
					}
				}
			}
			if($mode=='chat')
			{
				if($submit)
				{
					if($config_name=='ppkbb_chat_umclean')
					{
						$ppkbb_chat_umclean = my_split_config($this->new_config[$config_name], $config_map[$config_name][0], $config_map[$config_name][1], $config_map[$config_name][2]);
						if($ppkbb_chat_umclean[0])
						{
							$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE lastaccess < {$dt}";
							$db->sql_query($sql);
							$ppkbb_chat_umclean[0]=0;
						}
						if($ppkbb_chat_umclean[1])
						{
							$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE lastaccess > {$dt}";
							$db->sql_query($sql);
							$ppkbb_chat_umclean[1]=0;
						}
						if($ppkbb_chat_umclean[2] && $config['ppkbb_chat_marchive'][0])
						{
							$sql="DELETE FROM ".PPKCHAT_MESSAGES_TABLE.' WHERE date < '.($dt - ($config['ppkbb_chat_marchive'][0]));
							$db->sql_query($sql);
							$ppkbb_chat_umclean[2]=0;
						}
						if($ppkbb_chat_umclean[3] && $config['ppkbb_chat_marchive'][0])
						{
							$sql="SELECT id FROM ".PPKCHAT_MESSAGES_TABLE.' ORDER BY id DESC LIMIT '.($config['ppkbb_chat_messdisplay']-1).', 1';
							$result=$db->sql_query($sql);
							$last_mess_id=$db->sql_fetchrow($result);
							$db->sql_freeresult($result);
							$last_mess_id = @$last_mess_id['id'] ? $last_mess_id['id'] : 0;
							if($last_mess_id)
							{
								$sql="DELETE FROM ".PPKCHAT_MESSAGES_TABLE." WHERE id < {$last_mess_id} AND date > ".($dt - ($config['ppkbb_chat_marchive'][0]));
								$db->sql_query($sql);
							}
							$ppkbb_chat_umclean[3]=0;
						}
						$config_value=$this->new_config['ppkbb_chat_umclean']=implode(' ', $ppkbb_chat_umclean);
					}
					else if($config_name=='ppkbb_index_chat')
					{
						$ppkbb_index_chat=$this->new_config[$config_name];
						if($ppkbb_index_chat)
						{
							$sql="SELECT forumas FROM ".FORUMS_TABLE." WHERE forum_id='{$ppkbb_index_chat}' AND forumas=2";
							$result=$db->sql_query($sql);
							if(!$db->sql_fetchrow($result))
							{
								$ppkbb_index_chat=0;
							}
							$db->sql_freeresult($result);
						}
						$config_value=$this->new_config['ppkbb_index_chat']=$ppkbb_index_chat;
					}
					else if($config_name=='ppkbb_index_chat2')
					{
						$ppkbb_index_chat2=$this->new_config[$config_name];
						if($ppkbb_index_chat2)
						{
							$sql="SELECT forumas FROM ".FORUMS_TABLE." WHERE forum_id='{$ppkbb_index_chat2}' AND forumas=2";
							$result=$db->sql_query($sql);
							if(!$db->sql_fetchrow($result))
							{
								$ppkbb_index_chat2=0;
							}
							$db->sql_freeresult($result);
						}
						$config_value=$this->new_config['ppkbb_index_chat2']=$ppkbb_index_chat2;
					}
				}
			}

			if ($config_name == 'email_function_name')
			{
				$this->new_config['email_function_name'] = trim(str_replace(array('(', ')'), array('', ''), $this->new_config['email_function_name']));
				$this->new_config['email_function_name'] = (empty($this->new_config['email_function_name']) || !function_exists($this->new_config['email_function_name'])) ? 'mail' : $this->new_config['email_function_name'];
				$config_value = $this->new_config['email_function_name'];
			}

			if ($submit)
			{
				if(preg_match('/^ppkbb_/', $config_name))
				{
					set_tracker_config($config_name, $config_value);
				}
				else
				{
				set_config($config_name, $config_value);
				}

				if ($config_name == 'allow_quick_reply' && isset($_POST['allow_quick_reply_enable']))
				{
					enable_bitfield_column_flag(FORUMS_TABLE, 'forum_flags', log(FORUM_FLAG_QUICK_REPLY, 2));
				}
			}
		}

		// Store news and exclude ids
		if ($mode == 'feed' && $submit)
		{
			$cache->destroy('_feed_news_forum_ids');
			$cache->destroy('_feed_excluded_forum_ids');

			$this->store_feed_forums(FORUM_OPTION_FEED_NEWS, 'feed_news_id');
			$this->store_feed_forums(FORUM_OPTION_FEED_EXCLUDE, 'feed_exclude_id');
		}

		if($submit && (in_array($mode, $tracker_mode) || $mode=='chat'))
		{
			purge_tracker_config(true);
			//obtain_tracker_config();
		}

		if ($mode == 'auth')
		{
			// Retrieve a list of auth plugins and check their config values
			$auth_plugins = array();

			$dp = @opendir($phpbb_root_path . 'includes/auth');

			if ($dp)
			{
				while (($file = readdir($dp)) !== false)
				{
					if (preg_match('#^auth_(.*?)\.' . $phpEx . '$#', $file))
					{
						$auth_plugins[] = basename(preg_replace('#^auth_(.*?)\.' . $phpEx . '$#', '\1', $file));
					}
				}
				closedir($dp);

				sort($auth_plugins);
			}

			$updated_auth_settings = false;
			$old_auth_config = array();
			foreach ($auth_plugins as $method)
			{
				if ($method && file_exists($phpbb_root_path . 'includes/auth/auth_' . $method . '.' . $phpEx))
				{
					include_once($phpbb_root_path . 'includes/auth/auth_' . $method . '.' . $phpEx);

					$method = 'acp_' . $method;
					if (function_exists($method))
					{
						if ($fields = $method($this->new_config))
						{
							// Check if we need to create config fields for this plugin and save config when submit was pressed
							foreach ($fields['config'] as $field)
							{
								if (!isset($config[$field]))
								{
									set_config($field, '');
								}

								if (!isset($cfg_array[$field]) || strpos($field, 'legend') !== false)
								{
									continue;
								}

								$old_auth_config[$field] = $this->new_config[$field];
								$config_value = $cfg_array[$field];
								$this->new_config[$field] = $config_value;

								if ($submit)
								{
									$updated_auth_settings = true;
									set_config($field, $config_value);
								}
							}
						}
						unset($fields);
					}
				}
			}

			if ($submit && (($cfg_array['auth_method'] != $this->new_config['auth_method']) || $updated_auth_settings))
			{
				$method = basename($cfg_array['auth_method']);
				if ($method && in_array($method, $auth_plugins))
				{
					include_once($phpbb_root_path . 'includes/auth/auth_' . $method . '.' . $phpEx);

					$method = 'init_' . $method;
					if (function_exists($method))
					{
						if ($error = $method())
						{
							foreach ($old_auth_config as $config_name => $config_value)
							{
								set_config($config_name, $config_value);
							}
							trigger_error($error . adm_back_link($this->u_action), E_USER_WARNING);
						}
					}
					set_config('auth_method', basename($cfg_array['auth_method']));
				}
				else
				{
					trigger_error('NO_AUTH_PLUGIN', E_USER_ERROR);
				}
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars, (isset($config_map[$config_key]) ? $config_map[$config_key] : false));

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($display_vars['vars'][$config_key]);
		}

		if ($mode == 'auth')
		{
			$template->assign_var('S_AUTH', true);

			foreach ($auth_plugins as $method)
			{
				if ($method && file_exists($phpbb_root_path . 'includes/auth/auth_' . $method . '.' . $phpEx))
				{
					$method = 'acp_' . $method;
					if (function_exists($method))
					{
						$fields = $method($this->new_config);

						if ($fields['tpl'])
						{
							$template->assign_block_vars('auth_tpl', array(
								'TPL'	=> $fields['tpl'])
							);
						}
						unset($fields);
					}
				}
			}
		}
	}

	/**
	* Select auth method
	*/
	function select_auth_method($selected_method, $key = '')
	{
		global $phpbb_root_path, $phpEx;

		$auth_plugins = array();

		$dp = @opendir($phpbb_root_path . 'includes/auth');

		if (!$dp)
		{
			return '';
		}

		while (($file = readdir($dp)) !== false)
		{
			if (preg_match('#^auth_(.*?)\.' . $phpEx . '$#', $file))
			{
				$auth_plugins[] = preg_replace('#^auth_(.*?)\.' . $phpEx . '$#', '\1', $file);
			}
		}
		closedir($dp);

		sort($auth_plugins);

		$auth_select = '';
		foreach ($auth_plugins as $method)
		{
			$selected = ($selected_method == $method) ? ' selected="selected"' : '';
			$auth_select .= '<option value="' . $method . '"' . $selected . '>' . ucfirst($method) . '</option>';
		}

		return $auth_select;
	}

	/**
	* Select mail authentication method
	*/
	function mail_auth_select($selected_method, $key = '')
	{
		global $user;

		$auth_methods = array('PLAIN', 'LOGIN', 'CRAM-MD5', 'DIGEST-MD5', 'POP-BEFORE-SMTP');
		$s_smtp_auth_options = '';

		foreach ($auth_methods as $method)
		{
			$s_smtp_auth_options .= '<option value="' . $method . '"' . (($selected_method == $method) ? ' selected="selected"' : '') . '>' . $user->lang['SMTP_' . str_replace('-', '_', $method)] . '</option>';
		}

		return $s_smtp_auth_options;
	}

	/**
	* Select full folder action
	*/
	function full_folder_select($value, $key = '')
	{
		global $user;

		return '<option value="1"' . (($value == 1) ? ' selected="selected"' : '') . '>' . $user->lang['DELETE_OLDEST_MESSAGES'] . '</option><option value="2"' . (($value == 2) ? ' selected="selected"' : '') . '>' . $user->lang['HOLD_NEW_MESSAGES_SHORT'] . '</option>';
	}

	function select_tracker_forums($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true, 1);

		$selected = array();
		if(isset($config[$key]))
		{
			if(is_array($config[$key]))
			{
				$selected = $config[$key];
			}
			else if(strlen($config[$key]) > 0)
			{
				$selected = explode(',', $config[$key]);
			}
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;

	}
	function select_all_forums($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, false, false, false, true);

		$selected = array();
		if(isset($config[$key]))
		{
			if(is_array($config[$key]))
			{
				$selected = $config[$key];
			}
			else if(strlen($config[$key]) > 0)
			{
				$selected = explode(',', $config[$key]);
			}
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		$s_forum_options.='<option value="0"'.(in_array(0, $selected) ? ' selected="selected"' : '').'>'.$user->lang['FORUM_INDEX'].'</option>';
		foreach ($forum_list as $f_id => $f_row)
		{
			$f_row['padding']='&nbsp;'.$f_row['padding'];
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;

	}
	function select_forums($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true, 0, 1);

		$selected = array();
		if(isset($config[$key]))
		{
			if(is_array($config[$key]))
			{
				$selected = $config[$key];
			}
			else if(strlen($config[$key]) > 0)
			{
				$selected = explode(',', $config[$key]);
			}
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;

	}
	function select_chat_forums($value, $key)
	{
		global $user, $config, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true, 2);

		$selected = array();
		if(isset($config[$key]))
		{
			if(is_array($config[$key]))
			{
				$selected = $config[$key];
			}
			else if(strlen($config[$key]) > 0)
			{
				$selected = explode(',', $config[$key]);
			}
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]"><option value="0"></option>';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;

	}


	/**
	* Select ip validation
	*/
	function select_ip_check($value, $key = '')
	{
		$radio_ary = array(4 => 'ALL', 3 => 'CLASS_C', 2 => 'CLASS_B', 0 => 'NO_IP_VALIDATION');

		return h_radio('config[ip_check]', $radio_ary, $value, $key);
	}
	/**
	* Select referer validation
	*/
	function select_ref_check($value, $key = '')
	{
		$radio_ary = array(REFERER_VALIDATE_PATH => 'REF_PATH', REFERER_VALIDATE_HOST => 'REF_HOST', REFERER_VALIDATE_NONE => 'NO_REF_VALIDATION');

		return h_radio('config[referer_validation]', $radio_ary, $value, $key);
	}
	/**
	* Select account activation method
	*/
	function select_acc_activation($selected_value, $value)
	{
		global $user, $config;

		$act_ary = array(
		  'ACC_DISABLE' => USER_ACTIVATION_DISABLE,
		  'ACC_NONE' => USER_ACTIVATION_NONE,
		);
		if ($config['email_enable'])
		{
			$act_ary['ACC_USER'] = USER_ACTIVATION_SELF;
			$act_ary['ACC_ADMIN'] = USER_ACTIVATION_ADMIN;
		}
		$act_options = '';

		foreach ($act_ary as $key => $value)
		{
			$selected = ($selected_value == $value) ? ' selected="selected"' : '';
			$act_options .= '<option value="' . $value . '"' . $selected . '>' . $user->lang[$key] . '</option>';
		}

		return $act_options;
	}

	/**
	* Maximum/Minimum username length
	*/
	function username_length($value, $key = '')
	{
		global $user;

		return '<input id="' . $key . '" type="text" size="3" maxlength="3" name="config[min_name_chars]" value="' . $value . '" /> ' . $user->lang['MIN_CHARS'] . '&nbsp;&nbsp;<input type="text" size="3" maxlength="3" name="config[max_name_chars]" value="' . $this->new_config['max_name_chars'] . '" /> ' . $user->lang['MAX_CHARS'];
	}

	/**
	* Allowed chars in usernames
	*/
	function select_username_chars($selected_value, $key)
	{
		global $user;

		$user_char_ary = array('USERNAME_CHARS_ANY', 'USERNAME_ALPHA_ONLY', 'USERNAME_ALPHA_SPACERS', 'USERNAME_LETTER_NUM', 'USERNAME_LETTER_NUM_SPACERS', 'USERNAME_ASCII');
		$user_char_options = '';
		foreach ($user_char_ary as $user_type)
		{
			$selected = ($selected_value == $user_type) ? ' selected="selected"' : '';
			$user_char_options .= '<option value="' . $user_type . '"' . $selected . '>' . $user->lang[$user_type] . '</option>';
		}

		return $user_char_options;
	}

	/**
	* Maximum/Minimum password length
	*/
	function password_length($value, $key)
	{
		global $user;

		return '<input id="' . $key . '" type="text" size="3" maxlength="3" name="config[min_pass_chars]" value="' . $value . '" /> ' . $user->lang['MIN_CHARS'] . '&nbsp;&nbsp;<input type="text" size="3" maxlength="3" name="config[max_pass_chars]" value="' . $this->new_config['max_pass_chars'] . '" /> ' . $user->lang['MAX_CHARS'];
	}

	/**
	* Required chars in passwords
	*/
	function select_password_chars($selected_value, $key)
	{
		global $user;

		$pass_type_ary = array('PASS_TYPE_ANY', 'PASS_TYPE_CASE', 'PASS_TYPE_ALPHA', 'PASS_TYPE_SYMBOL');
		$pass_char_options = '';
		foreach ($pass_type_ary as $pass_type)
		{
			$selected = ($selected_value == $pass_type) ? ' selected="selected"' : '';
			$pass_char_options .= '<option value="' . $pass_type . '"' . $selected . '>' . $user->lang[$pass_type] . '</option>';
		}

		return $pass_char_options;
	}

	/**
	* Select bump interval
	*/
	function bump_interval($value, $key)
	{
		global $user;

		$s_bump_type = '';
		$types = array('m' => 'MINUTES', 'h' => 'HOURS', 'd' => 'DAYS');
		foreach ($types as $type => $lang)
		{
			$selected = ($this->new_config['bump_type'] == $type) ? ' selected="selected"' : '';
			$s_bump_type .= '<option value="' . $type . '"' . $selected . '>' . $user->lang[$lang] . '</option>';
		}

		return '<input id="' . $key . '" type="text" size="3" maxlength="4" name="config[bump_interval]" value="' . $value . '" />&nbsp;<select name="config[bump_type]">' . $s_bump_type . '</select>';
	}

	/**
	* Board disable option and message
	*/
	function board_disable($value, $key)
	{
		global $user;

		$radio_ary = array(1 => 'YES', 0 => 'NO');

		return h_radio('config[board_disable]', $radio_ary, $value) . '<br /><input id="' . $key . '" type="text" name="config[board_disable_msg]" maxlength="255" size="40" value="' . $this->new_config['board_disable_msg'] . '" />';
	}

	/**
	* Global quick reply enable/disable setting and button to enable in all forums
	*/
	function quick_reply($value, $key)
	{
		global $user;

		$radio_ary = array(1 => 'YES', 0 => 'NO');

		return h_radio('config[allow_quick_reply]', $radio_ary, $value) .
			'<br /><br /><input class="button2" type="submit" id="' . $key . '_enable" name="' . $key . '_enable" value="' . $user->lang['ALLOW_QUICK_REPLY_BUTTON'] . '" />';
	}


	/**
	* Select default dateformat
	*/
	function dateformat_select($value, $key)
	{
		global $user, $config;

		// Let the format_date function operate with the acp values
		$old_tz = $user->timezone;
		$old_dst = $user->dst;

		$user->timezone = $config['board_timezone'] * 3600;
		$user->dst = $config['board_dst'] * 3600;

		$dateformat_options = '';

		foreach ($user->lang['dateformats'] as $format => $null)
		{
			$dateformat_options .= '<option value="' . $format . '"' . (($format == $value) ? ' selected="selected"' : '') . '>';
			$dateformat_options .= $user->format_date(time(), $format, false) . ((strpos($format, '|') !== false) ? $user->lang['VARIANT_DATE_SEPARATOR'] . $user->format_date(time(), $format, true) : '');
			$dateformat_options .= '</option>';
		}

		$dateformat_options .= '<option value="custom"';
		if (!isset($user->lang['dateformats'][$value]))
		{
			$dateformat_options .= ' selected="selected"';
		}
		$dateformat_options .= '>' . $user->lang['CUSTOM_DATEFORMAT'] . '</option>';

		// Reset users date options
		$user->timezone = $old_tz;
		$user->dst = $old_dst;

		return "<select name=\"dateoptions\" id=\"dateoptions\" onchange=\"if (this.value == 'custom') { document.getElementById('" . addslashes($key) . "').value = '" . addslashes($value) . "'; } else { document.getElementById('" . addslashes($key) . "').value = this.value; }\">$dateformat_options</select>
		<input type=\"text\" name=\"config[$key]\" id=\"$key\" value=\"$value\" maxlength=\"30\" />";
	}

	/**
	* Select multiple forums
	*/
	function select_news_forums($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true);

		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$f_row['selected'] = phpbb_optionget(FORUM_OPTION_FEED_NEWS, $f_row['forum_options']);

			$s_forum_options .= '<option value="' . $f_id . '"' . (($f_row['selected']) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;
	}

	function select_exclude_forums($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true);

		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$f_row['selected'] = phpbb_optionget(FORUM_OPTION_FEED_EXCLUDE, $f_row['forum_options']);

			$s_forum_options .= '<option value="' . $f_id . '"' . (($f_row['selected']) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;
	}

	function store_feed_forums($option, $key)
	{
		global $db, $cache;

		// Get key
		$values = request_var($key, array(0 => 0));

		// Empty option bit for all forums
		$sql = 'UPDATE ' . FORUMS_TABLE . '
			SET forum_options = forum_options - ' . (1 << $option) . '
			WHERE ' . $db->sql_bit_and('forum_options', $option, '<> 0');
		$db->sql_query($sql);

		// Already emptied for all...
		if (sizeof($values))
		{
			// Set for selected forums
			$sql = 'UPDATE ' . FORUMS_TABLE . '
				SET forum_options = forum_options + ' . (1 << $option) . '
				WHERE ' . $db->sql_in_set('forum_id', $values);
			$db->sql_query($sql);
		}

		// Empty sql cache for forums table because options changed
		$cache->destroy('sql', FORUMS_TABLE);
	}

}

?>
