<?php
/**
*
* @package ppkBB3cker
* @version $Id: mcp_topic_add1.php 1.000 2014-04-22 11:05:45 PPK $
* @copyright (c) 2014 PPK
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

$torrent_link=1;

$torrent_statuses=get_torrent_statuses();
if(!isset($torrent_statuses['TRACKER_FORB_MARK'][$torrents[$torrent_id]['forb']]))
{
	$torrents[$torrent_id]['forb']=0;
}

$is_admod=$auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_') ? 1 : 0;

$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);

$assign_vars=$torrent_stat=$sort_opt=$postrow_headers=$postrow_header=array();

if($is_candowntorr)
{
	$is_canusefree = ($auth->acl_get('u_canusefree') && $auth->acl_get('f_canusefree', $forum_id)) && $user->data['is_registered'] ? 1 : 0;
	$is_canusebonus = ($auth->acl_get('u_canusebonus') && $auth->acl_get('f_canusebonus', $forum_id)) && $user->data['is_registered'] ? 1 : 0;
	$is_canskiprcheck = $user->data['is_registered'] && $user->data['user_id']!=$poster_id ? (($auth->acl_get('u_canskiprcheck') && $auth->acl_get('f_canskiprcheck', $forum_id)) ? 1 : 0) : 1;
	$is_canskiprequpload = $user->data['is_registered'] && $user->data['user_id']!=$poster_id ? (($auth->acl_get('u_canskiprequpload') && $auth->acl_get('f_canskiprequpload', $forum_id)) ? 1 : 0) : 1;
	$is_canskipreqratio = $user->data['is_registered'] && $user->data['user_id']!=$poster_id ? (($auth->acl_get('u_canskipreqratio') && $auth->acl_get('f_canskipreqratio', $forum_id)) ? 1 : 0) : 1;
	$is_cansetforb = ($auth->acl_get('u_cansetforb') && $auth->acl_get('f_cansetforb', $forum_id)) ? 1 : 0;
	$is_cansetsticky = $auth->acl_get('f_sticky', $forum_id) ? 1 : 0;

	$torrents[$torrent_id]['forb'] < 1 || ($torrents[$torrent_id]['forb'] > 0 && $user->data['user_id']==$poster_id && in_array($torrents[$torrent_id]['forb'], $config['ppkbb_tcauthor_candown'])) ? '' : $torrent_link=0;

	$t_elapsed = intval(($dt - $torrents[$torrent_id]['added']) / 3600);
	$t_wait=-1;
	if(!$is_canskiprcheck && $config['ppkbb_tcwait_time'])
	{
		$t_wait=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time'], 'up');

		if ($t_wait > 0)
		{
			$t_elapsed < $t_wait ? $t_wait=$t_wait - $t_elapsed : $t_wait=-1;
		}
		$t_wait >= 0 ? $torrent_link=0 : '';
	}

	$t_wait2=-1;
	if(!$is_canskiprcheck && $config['ppkbb_tcwait_time2'])
	{
		$t_wait2=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time2']);
		if ($t_wait2 > 0)
		{
			$t_elapsed < $t_wait2 ? $t_wait2=$t_wait2 - $t_elapsed : $t_wait2=-1;
		}
		$t_wait2 >= 0 ? $torrent_link=0 : '';
	}
}

$torrent_seeders=$torrents[$torrent_id]['seeders']-$torrents[$torrent_id]['rem_seeders'];
$torrent_times_completed=$torrents[$torrent_id]['times_completed']-$torrents[$torrent_id]['rem_times_completed'];
if(!$torrent_seeders)
{
	$seed_percent=0;
}
else if($torrent_times_completed && $torrent_seeders < $torrent_times_completed)
{
	$seed_percent=my_int_val($torrent_seeders * 100 / $torrent_times_completed);
}
else
{
	$seed_percent=100;
}

if(!class_exists('timedelta'))
{
$user->add_lang('mods/posts_merging');
require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
}
$td = new timedelta();

$torrent_stat['TS_AUTHOR']=get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
$torrent_stat['TS_ADDED']=$user->format_date($torrents[$torrent_id]['added']);
$torrent_stat['TS_ADDED_LEFT']=$td->spelldelta($torrents[$torrent_id]['added'], $dt);
$torrents[$torrent_id]['forb']!=0 && isset($torrent_statuses['TRACKER_FORB_MARK'][$torrents[$torrent_id]['forb']]) ? $torrent_stat['TS_TSTATUS']=$torrent_statuses['TRACKER_FORB_MARK'][$torrents[$torrent_id]['forb']] : '';
if($torrents[$torrent_id]['forb_user_id'])
{
	$torrent_stat['TS_USTATUS']=get_username_string('full', $torrents[$torrent_id]['forb_user_id'], $torrents[$torrent_id]['username'], $torrents[$torrent_id]['user_colour']);
	$torrent_stat['TS_USTATUS_DATE']=$user->format_date($torrents[$torrent_id]['forb_date']);
}
$torrents[$torrent_id]['forb_reason'] ? $torrent_stat['TS_REASON']=$torrents[$torrent_id]['forb_reason'] : '';
$torrent_stat['TS_SEEDERS']=$torrents[$torrent_id]['seeders'];
$torrent_stat['TS_LEECHERS']=$torrents[$torrent_id]['leechers'];
$torrent_stat['TS_COMPLETED']=$torrents[$torrent_id]['times_completed'];

$torrent_stat['TS_REAL_SEEDERS']=$torrents[$torrent_id]['real_seeders'];
$torrent_stat['TS_REAL_LEECHERS']=$torrents[$torrent_id]['real_leechers'];
$torrent_stat['TS_REAL_COMPLETED']=$torrents[$torrent_id]['real_times_completed'];
$torrent_stat['TS_REM_SEEDERS']=(isset($torrents[$torrent_id]['rem_seeders']) ? $torrents[$torrent_id]['rem_seeders'] : 0);
$torrent_stat['TS_REM_LEECHERS']=(isset($torrents[$torrent_id]['rem_leechers']) ? $torrents[$torrent_id]['rem_leechers'] : 0);
$torrent_stat['TS_REM_COMPLETED']=(isset($torrents[$torrent_id]['rem_times_completed']) ? $torrents[$torrent_id]['rem_times_completed'] : 0);

$torrent_stat['TS_HEALTH']=get_torrent_health($torrents[$torrent_id]['seeders'], $torrents[$torrent_id]['leechers']);
//if($torrents[$torrent_id]['times_completed'])
//{
	$torrent_stat['TS_SPERCENT']=$seed_percent;
	$torrent_stat['TS_SPERCENT_WO_REM']=false;
//}
$torrent_stat['TS_SIZE']=get_formatted_filesize($torrents[$torrent_id]['size']);
$torrent_stat['TS_SIZE2']=number_format($torrents[$torrent_id]['size'], 0, '.', ' ');

//$dt - $torrents[$torrent_id]['added'] < $config['ppkbb_tctstat_ctime'] && $config['ppkbb_tctstat_ctime'] > 1 ? $config['ppkbb_tctstat_ctime'] = 1 : $config['ppkbb_tctstat_ctime']=$config['ppkbb_tctstat_ctime'];

$tsl_speed=my_split_config($torrents[$torrent_id]['tsl_speed'], 3, 'my_int_val');
$total_updown_speed=array();
if($torrents[$torrent_id]['forb'] > 0 || ($config['ppkbb_tcannounce_interval'] && $dt-$torrents[$torrent_id]['lastseed']>$config['ppkbb_tcannounce_interval'] && $dt-$torrents[$torrent_id]['lastleech']>$config['ppkbb_tcannounce_interval']))
{
	$total_updown_speed['up_speed']=$total_updown_speed['down_speed']=0;
}
else
{
	/*if($dt - $tsl_speed[2] > $config['ppkbb_tctstat_ctime'])
	{
		$sql="SELECT SUM(s.uploadoffset/(s.last_action-s.prev_action)) up_speed, SUM(s.downloadoffset/(s.last_action-s.prev_action)) down_speed FROM ".TRACKER_SNATCHED_TABLE." s WHERE torrent='{$torrent_id}' AND s.last_action > ".($dt-$config['ppkbb_tcdead_time'])."";
		$result=$db->sql_query($sql);//, $config['ppkbb_tctstat_ctime']
		$total_updown_speed=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET tsl_speed='".intval($total_updown_speed['up_speed'])." ".intval($total_updown_speed['down_speed'])." {$dt}' WHERE id='{$torrent_id}'";
		$result=$db->sql_query($sql);
	}
	else
	{*/
		$total_updown_speed['up_speed']=$dt-$torrents[$torrent_id]['lastseed']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[0];
		$total_updown_speed['down_speed']=$dt-$torrents[$torrent_id]['lastleech']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[1];
	//}
}
$torrent_stat['TS_SSPEED']=get_formatted_filesize($total_updown_speed['up_speed'], 1, false, 1);
$torrent_stat['TS_LSPEED']=get_formatted_filesize($total_updown_speed['down_speed'], 1, false,  1);

$last_seedleech=array();
$last_seedleech['last_seed']=$torrents[$torrent_id]['lastseed'];
$last_seedleech['last_leech']=$torrents[$torrent_id]['lastleech'];

isset($torrents[$torrent_id]['rem_seeders']) && $torrents[$torrent_id]['rem_seeders'] && $torrents[$torrent_id]['lastremote'] > $last_seedleech['last_seed'] ? $last_seedleech['last_seed']=$torrents[$torrent_id]['lastremote'] : '';
$torrent_stat['TS_LSEED']=($last_seedleech['last_seed'] ? $td->spelldelta($last_seedleech['last_seed'], $dt) : false);

$torrents[$torrent_id]['rem_leechers'] && $torrents[$torrent_id]['lastremote'] > $last_seedleech['last_leech'] ? $last_seedleech['last_leech']=$torrents[$torrent_id]['lastremote'] : '';
$torrent_stat['TS_LLEECH']=($last_seedleech['last_leech'] ? $td->spelldelta($last_seedleech['last_leech'], $dt) : false);

$torrent_stat['TS_THANKS']=$torrents[$torrent_id]['thanks'];

$torrent_stat['TS_PRIVATE']=$torrents[$torrent_id]['private'] ? true : false;
if($is_candowntorr && $config['ppkbb_torr_blocks'][9])
{
	$torrents[$torrent_id]['info_hash']=bin2hex($torrents[$torrent_id]['info_hash']);
	$torrent_stat['TS_HASH']=wordwrap($torrents[$torrent_id]['info_hash'], 20, '<br />', true);
	$torrent_stat['TS_HASH2']=$torrents[$torrent_id]['info_hash'];
}

$torrent_top_notice=$torrent_bottom_notice=false;
if($user->data['is_registered'])
{
	$config['ppkbb_noticedisclaimer_blocks'][1] && $user->lang['TORRENT_TOP_NOTICE'] ? $torrent_top_notice=$user->lang['TORRENT_TOP_NOTICE'] : '';
	$config['ppkbb_noticedisclaimer_blocks'][2] && $user->lang['TORRENT_BOTTOM_NOTICE'] ? $torrent_bottom_notice=$user->lang['TORRENT_BOTTOM_NOTICE'] : '';
}
else
{
	$config['ppkbb_noticedisclaimer_blocks'][3] && $user->lang['TORRENT_TOP_NOTICE_GUEST'] ? $torrent_top_notice=$user->lang['TORRENT_TOP_NOTICE_GUEST'] : '';
	$config['ppkbb_noticedisclaimer_blocks'][4] && $user->lang['TORRENT_BOTTOM_NOTICE_GUEST'] ? $torrent_bottom_notice=$user->lang['TORRENT_BOTTOM_NOTICE_GUEST'] : '';
}

$postrow=array_merge($postrow, array(

	'S_HAS_TRACKER_TORRENT' => (isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']) && /*$torrent_guest && */$config['ppkbb_torr_blocks'][1]) ? true : false,
	'S_HAS_TRACKER_POSTER' => (isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']) && $config['ppkbb_torr_blocks'][2]) ? true : false,
	'S_HAS_TRACKER_SCREENSHOT' => (isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']) && $config['ppkbb_torr_blocks'][3]) ? true : false,

	'TRACKER_TORRENTS_COUNT' => isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']) > 1 ? true : false,
	'TRACKER_POSTERS_COUNT' => isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']) > 1 ? true : false,
	'TRACKER_SCREENSHOTS_COUNT' => isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']) > 1 ? true : false,

	'TORRENT_INFO_STAT' => $torrent_stat && $config['ppkbb_torr_blocks'][0] ? true : false,

	'S_TORRENT_TOP_NOTICE' => $torrent_top_notice,
	'S_TORRENT_BOTTOM_NOTICE' => $torrent_bottom_notice,

	'FORUM_IMAGE' => $forum_image ? $phpbb_root_path.$forum_image : '',
));

//if($config['ppkbb_torr_blocks'][0])
//{
	if(sizeof($torrent_stat))
	{
		$postrow=array_merge($postrow, $torrent_stat);
	}
//}
?>
