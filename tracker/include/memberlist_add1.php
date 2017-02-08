<?php
/**
*
* @package ppkBB3cker
* @version $Id: memberlist_add1.php 1.000 2008-11-14 17:30:00 PPK $
* @copyright (c) 2008 PPK
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

if(!function_exists('display_forums'))
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
}

$is_admod=$auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_') ? 1 : 0;

$dt=time();
if($user->data['is_registered'])
{
	$u_update=$tracker_cron=array();
	$user_tracker_data=my_split_config($member['user_tracker_data'], 5, 'my_int_val');
	$u_update[0]=$u_update[1]=$u_update[2]=0;
	if($config['ppkbb_tccron_jobs'][0] && $dt-$user_tracker_data[0] > $config['ppkbb_tccron_jobs'][0])
	{
		$u_update[0]=1;
	}
	if($config['ppkbb_tccron_jobs'][1] && $dt-$user_tracker_data[1] > $config['ppkbb_tccron_jobs'][1])
	{
		$u_update[1]=1;
	}
	if(sizeof($u_update) && array_sum($u_update))
	{
		$u_update[3]=$member['user_id'];
		$u_update[4]=$member['user_tracker_data'];
		$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, forum_id, added) VALUES('u_update', '".$db->sql_escape(serialize($u_update))."', '0', '{$dt}')";
		$db->sql_query($sql);
		$cron_id=$db->sql_nextid();
		if($cron_id)
		{
			$tracker_cron[]=$cron_id;
		}
		if(sizeof($tracker_cron))
		{
			$cron_id=implode('&amp;id[]=', $tracker_cron);
			$template->assign_block_vars('tracker_cron', array(
				'CRON_TASK'=>'<img src="' . append_sid($phpbb_root_path . 'tracker/cron.' . $phpEx, 'id[]='.$cron_id) . '" alt="cron" width="1" height="1" />'
				)
			);
		}
	}
}

$template->assign_vars(array(
	'TRACKER_USER_TORRENTS'		=> $member['user_torrents'],
	'TRACKER_USER_COMMENTS'		=> $member['user_comments'],
	'U_TRACKER_USER_TORRENTS'		=> append_sid("{$phpbb_root_path}search.$phpEx", "search_id=egosearch&amp;torrent=my&amp;u={$user_id}"),
	'U_TRACKER_USER_COMMENTS'		=> append_sid("{$phpbb_root_path}search.$phpEx", "search_id=egosearch&amp;torrent=mycomment&amp;u={$user_id}"),
	'TRACKER_USER_UPLOAD'		=> get_formatted_filesize($member['user_uploaded']),
	'TRACKER_USER_UPLOAD_SELF'		=> get_formatted_filesize($member['user_uploaded_self']),
	'TRACKER_USER_DOWNLOAD'		=> get_formatted_filesize($member['user_downloaded']),
	'TRACKER_USER_REALDOWNLOAD'		=> get_formatted_filesize($member['user_shadow_downloaded']),
	'TRACKER_USER_BONUS'		=> $member['user_bonus'],
	'TRACKER_USER_YTHANKS'		=> $member['user_fromthanks_count'],
	'TRACKER_USER_TYTHANKS'		=> $member['user_tothanks_count'],
	'TRACKER_USER_RRATIO'		=> get_ratio_alias(get_ratio($member['user_uploaded'], $member['user_shadow_downloaded'])),
	'TRACKER_USER_RATIO'		=> get_ratio_alias(get_ratio($member['user_uploaded'], $member['user_downloaded'], $config['ppkbb_tcratio_start'], $member['user_bonus'])),

));

$torrent_info_curr='';

$torrent_info_curr_explain=$assign_vars=$torrent_info=$sort_opt=$postrow_header=$postrow_headers=array();
$i3=0;

$torrent_opt=$opt=request_var('opt', '');
$opt_sort=request_var('opts', '');
$opt_param=request_var('optp', '');
$opt_sort=='ASC' ? '' : $opt_sort='DESC';

$memberlist_url=$mua_url=append_sid("memberlist.{$phpEx}", "mode=viewprofile&amp;u={$user_id}");

$is_canviewmuastatr=$auth->acl_get('u_canviewmuastatr') || $member['user_id']==$user->data['user_id'] ? 1 : 0;
$is_canviewmuastatorr=$auth->acl_get('u_canviewmuastatorr') || $member['user_id']==$user->data['user_id'] ? 1 : 0;
if($is_canviewmuastatorr)
{
	$torrents_info=array('torrent', 'finished', 'seed', 'leech', 'tothanks', 'fromthanks', 'history', 'leave', '', 'downloads');
	foreach($torrents_info as $ik=>$iv)
	{
		if($iv)
		{
			if((!$config['ppkbb_torrent_statml'][$ik]) || (in_array($iv, array('tothanks', 'fromthanks')) && !$config['ppkbb_thanks_enable']))
			{
				($torrent_opt==$iv && !$config['ppkbb_torrent_statml'][$ik]) || (in_array($torrent_opt, array('tothanks', 'fromthanks')) && !$config['ppkbb_thanks_enable']) ? $torrent_opt='' : '';
				continue;
			}
			$torrent_info[$iv]='<a href="'.$memberlist_url.($torrent_opt!=$iv ? '&amp;opt='.$iv.'#opt' : '').'">'.$user->lang['TORRENT_INFO_HEADER_'.strtoupper($iv)].'</a>';
		}
	}
}
if(!class_exists('timedelta'))
{
$user->add_lang('mods/posts_merging');
require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
$td = new timedelta();
}
!$config['ppkbb_mua_countlist'] ? !$config['ppkbb_mua_countlist']=array(5, 10, 25, 50, 75, 100, 250, 500, 750, 1000, 5000, 10000) : $config['ppkbb_mua_countlist']=my_split_config($config['ppkbb_mua_countlist'], 0, 'my_int_val');
sort($config['ppkbb_mua_countlist']);
$mua_limit=$config['ppkbb_mua_countlist'][sizeof($config['ppkbb_mua_countlist'])-1];
$mua_add1inc=$phpbb_root_path.'tracker/include/';
$mua='memberlist';
$ex_fid_ary=array_keys($auth->acl_getf('!f_read', true));
switch($torrent_opt)
{
	case 'leave':
		include($mua_add1inc.'mua_add1_leave.'.$phpEx);
		break;

	case 'history':
		include($mua_add1inc.'mua_add1_history.'.$phpEx);
		break;

	case 'finished':
		include($mua_add1inc.'mua_add1_finished.'.$phpEx);
		break;

	case 'seed':
		include($mua_add1inc.'mua_add1_seed.'.$phpEx);
		break;

	case 'leech':
		include($mua_add1inc.'mua_add1_leech.'.$phpEx);
		break;

	case 'tothanks':
		include($mua_add1inc.'mua_add1_tothanks.'.$phpEx);
		break;

	case 'fromthanks':
		include($mua_add1inc.'mua_add1_fromthanks.'.$phpEx);
		break;

	case 'torrent':
		include($mua_add1inc.'mua_add1_torrent.'.$phpEx);
		break;

	case 'downloads':
		include($mua_add1inc.'mua_add1_downloads.'.$phpEx);
		break;

	default:
		$torrent_opt='';
		break;
}

$torrent_info_curr_explain[$opt]=true;
$template->assign_vars(array(
	'S_HAS_TORRENT_EXPLAIN'	=> $torrent_info_curr ? true : false,
	'S_HAS_TORRENT_EXPLAIN_TORRENT'	=> @$torrent_info_curr_explain['torrent'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FINISHED'	=> @$torrent_info_curr_explain['finished'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FROMTHANKS'=> @$torrent_info_curr_explain['fromthanks'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_TOTHANKS'	=> @$torrent_info_curr_explain['tothanks'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_HISTORY'	=> @$torrent_info_curr_explain['history'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEAVE'	=> @$torrent_info_curr_explain['leave'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_SEED'	=> @$torrent_info_curr_explain['seed'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEECH'	=> @$torrent_info_curr_explain['leech'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_DOWNLOADS'	=> @$torrent_info_curr_explain['downloads'] ? true : false,
	'S_IS_ADMOD' => $is_admod ? true : false,

	'TORRENT_INFO_OPT'=> sizeof($torrent_info) ? implode(' : ', $torrent_info) : false,
	'TRACKER_INFO_OPT'=> $is_canviewmuastatr ? true : false,
	'S_MUA_COUNTLIST_DEFAULT' => $config['ppkbb_mua_countlist'][0],
	'S_MUA_COUNTLIST' => implode(', ', $config['ppkbb_mua_countlist']),
));

if($assign_vars)
{
	foreach($assign_vars as $k2 => $v2)
	{
		$template->assign_block_vars($torrent_info_curr.'_option', $v2);
	}
}
if($postrow_headers)
{
	foreach($postrow_headers as $k2 => $v2)
	{
		$template->assign_block_vars('headers', array('VALUE' => $v2));
	}
}

if(sizeof($postrow_header))
{
	$template->assign_var('S_TORRENT_FOOTER', implode(' : ', $postrow_header));
}
?>
