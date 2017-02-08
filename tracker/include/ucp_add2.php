<?php
/**
*
* @package ppkBB3cker
* @version $Id: ucp_add2.php 1.000 2009-07-22 15:06:00 PPK $
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

include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

$template->assign_vars(array(
	'TRACKER_USER_TORRENTS'		=> $user->data['user_torrents'],
	'TRACKER_USER_COMMENTS'		=> $user->data['user_comments'],
	'TRACKER_USER_UPLOAD'		=> get_formatted_filesize($user->data['user_uploaded']),
	'TRACKER_USER_UPLOAD_SELF'		=> get_formatted_filesize($user->data['user_uploaded_self']),
	'TRACKER_USER_DOWNLOAD'		=> get_formatted_filesize($user->data['user_downloaded']),
	'TRACKER_USER_REALDOWNLOAD'		=> get_formatted_filesize($user->data['user_shadow_downloaded']),
	'TRACKER_USER_BONUS'		=> $user->data['user_bonus'],

	'TRACKER_USER_YTHANKS'		=> $user->data['user_fromthanks_count'],
	'TRACKER_USER_TYTHANKS'		=> $user->data['user_tothanks_count'],
	'TRACKER_USER_RRATIO'		=> get_ratio_alias(get_ratio($user->data['user_uploaded'], $user->data['user_shadow_downloaded'])),
	'TRACKER_USER_RATIO'		=> get_ratio_alias(get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus'])),


));
?>
