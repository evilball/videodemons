<?php
/**
*
* @package ppkBB3cker
* @version $Id: top.php 1.000 2010-04-19 10:54:00 PPK $
* @copyright (c) 2010 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user->add_lang('mods/ppkbb3cker_top');

if(!$config['ppkbb_tracker_top'][0])
{
	trigger_error($user->lang['TOP_DISABLED']);
}
if(!$auth->acl_get('u_canviewtrtop'))
{
	trigger_error($user->lang['TOP_NOAUTH']);
}
$start=$top_limit=0;
$top_limit=$config['ppkbb_tracker_top'][0];
$top_inc=$phpbb_root_path.'tracker/top/';

$dt=time();

$ex_fid_ary=array_keys($auth->acl_getf('!f_read', true));

$top_by=request_var('t', '');
$top_times=array('w' => $dt-(86400*7), 'm' => $dt-(86400*30), 'a' => 0);
$top_langs=array('w' => 'TOP_WEEK', 'm' => 'TOP_MONTH', 'a' => 'TOP_ALL');
!isset($top_times[$top_by]) ? $top_by='a' : '';
$top_time=$top_times[$top_by];

$user->lang[$top_langs[$top_by]]="<strong>{$user->lang[$top_langs[$top_by]]}</strong>";

$template->assign_vars(array(
	'U_TOP_WEEK' => append_sid("{$phpbb_root_path}top.{$phpEx}?t=w"),
	'U_TOP_MONTH' => append_sid("{$phpbb_root_path}top.{$phpEx}?t=m"),
	'U_TOP_ALL' => append_sid("{$phpbb_root_path}top.{$phpEx}?t=a"),
	'TOP_CURR' => $top_by,
	'TOP_BY_AUTHOR' => $config['ppkbb_tccron_jobs'][0] ? true : false,

	'TOP_BY_THANKS' => $config['ppkbb_thanks_enable'] ? true : false,
	)
);

include("{$top_inc}top_by_ratio.{$phpEx}");
include("{$top_inc}top_by_upload.{$phpEx}");
include("{$top_inc}top_by_torrents.{$phpEx}");

if($config['ppkbb_thanks_enable'])
{
	include("{$top_inc}top_by_thanks.{$phpEx}");
}

include("{$top_inc}top_by_downsum.{$phpEx}");

if($config['ppkbb_tccron_jobs'][0])
{
include("{$top_inc}top_by_author.{$phpEx}");
}
page_header(sprintf($user->lang['TRACKER_TOP'], $config['ppkbb_tracker_top'][0]));

$template->set_filenames(array(
	'body' => 'top_body.html')
);

page_footer();

?>
