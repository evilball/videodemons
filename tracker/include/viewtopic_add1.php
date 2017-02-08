<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1.php 1.000 2008-11-14 17:20:00 PPK $
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

$torrent_link=1;

$torrent_statuses=get_torrent_statuses();
if(!isset($torrent_statuses['TRACKER_FORB_MARK'][$torrents[$torrent_id]['forb']]))
{
	$update_tstatus[$torrents[$torrent_id]['forb']]=$torrents[$torrent_id]['forb'];
	$torrents[$torrent_id]['forb']=0;
}

$is_admod=$auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_') ? 1 : 0;

$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);

//!$user->data['is_registered'] && !$config['ppkbb_tcguests_enabled'][0] ? $torrent_guest=0 : $torrent_guest=1;
$torrent_info_curr='';
$torrent_info_curr_explain=$assign_vars=$torrent_info=$torrent_stat=$torrent_action=$sort_opt=$postrow_headers=$postrow_header=array();
$torrent_opt=$opt=request_var('opt', '');
$opt_sort=request_var('opts', '');
$opt_param=request_var('optp', '');
$opt_sort=='ASC' ? '' : $opt_sort='DESC';

!$is_candowntorr/* || !$torrent_guest || !$torrent_link*/ ? $torrent_opt='' : '';

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
	$is_canviewvtstats=$auth->acl_get('u_canviewvtstats') && $auth->acl_get('f_canviewvtstats', $forum_id) ? 1 : 0;
	if(/*$torrent_guest && */$torrent_link && $is_canviewvtstats)
	{
		$torrents_info=array('filelist', 'finished', 'seed', 'leech', 'thanks', 'history', 'leave', '', 'remote', 'downloads');
		foreach($torrents_info as $ik=>$iv)
		{
			if($iv)
			{
				if(($iv=='remote' && !$config['ppkbb_tcenable_rannounces'][0]) || (!$config['ppkbb_torrent_statvt'][$ik]) || (in_array($iv, array('thanks')) && !$config['ppkbb_thanks_enable']))
				{
					($torrent_opt==$iv && !$config['ppkbb_torrent_statml'][$ik]) || (in_array($torrent_opt, array('thanks')) && !$config['ppkbb_thanks_enable']) ? $torrent_opt='' : '';
					continue;
				}
				$tracker_viewtopic_url=append_sid("{$phpbb_root_path}viewtopic.{$phpEx}?p={$row['post_id']}&amp;page=vt").($torrent_opt!=$iv ? "&amp;opt={$iv}#opt" : '');
				$torrent_info[$iv]='<a class="torrent_stat" href="'.$tracker_viewtopic_url.'">'.$user->lang['TORRENT_INFO_HEADER_'.strtoupper($iv)].'</a>';
			}
		}
	}
}

if($is_candowntorr && $is_cansetforb)
{
	$set_forb=request_var('set_forb', '');
	$forb_val=request_var('forb_val', 0);
	!isset($torrent_statuses['TRACKER_FORB_MARK'][$forb_val]) || $forb_val > 99 || $forb_val < -99 ? $forb_val=0 : '';
	!$is_cansetforb && ($forb_val > -50 || $torrents[$torrent_id]['forb'] > 0) ? $set_forb=0 : '';
	$forb_locked=request_var('forb_locked', 0) ? 1 : 0;
	$forb_closed=request_var('forb_closed', 0) ? 1 : 0;
	$forb_val==1 ? $forb_locked=1 : '';
	$forb_val==0 ? $forb_locked=0 : '';
	if($set_forb)
	{
		$forb_reason =!empty($_POST['forb_reason']) ? utf8_normalize_nfc(request_var('forb_reason', '', true)) : '';
		$result=$db->sql_query('UPDATE '. POSTS_TABLE ." SET post_edit_locked='$forb_locked' WHERE post_id='{$row['post_id']}'");
		$result=$db->sql_query('UPDATE '. TOPICS_TABLE ." SET topic_status='$forb_closed' WHERE topic_id='{$row['topic_id']}'");
		$result=$db->sql_query('UPDATE '. TRACKER_TORRENTS_TABLE ." SET forb='$forb_val', forb_reason='".$db->sql_escape(truncate_string($forb_reason, 255, 255, false))."', forb_date='$dt', forb_user_id='{$user->data['user_id']}' WHERE post_msg_id='{$row['post_id']}'");
		if($user->data['user_id']!=$poster_id && (($config['ppkbb_tstatus_notify'][0] && $forb_val < 1 && $torrents[$torrent_id]['forb'] > 0) || ($config['ppkbb_tstatus_notify'][1] && $forb_val  > 0 && $torrents[$torrent_id]['forb'] < 1)))
		{
			$approve=array(
				'from_user_id'=>$user->data['user_id'],
				'icon_id'=>0,
				'from_user_ip'=>$user->ip,
				'from_username'=>$user->data['username'],
				'enable_bbcode'=>0,
				'enable_smilies'=>0,
				'enable_urls'=>0,
				'enable_sig'=>0,
				'message'=>sprintf($user->lang['TORRENT_NOTIFY_TEXT'], append_sid($phpbb_root_path."viewtopic.{$phpEx}?f={$forum_id}&amp;t={$topic_id}"), $topic_data['topic_title'], $torrent_statuses['TRACKER_FORB_REASON'][$forb_val], $torrent_statuses['TRACKER_FORB_REASON'][$torrents[$torrent_id]['forb']], get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour']), htmlspecialchars($forb_reason), $user->format_date($dt, 'Y-m-d H:i:s')),
				'bbcode_bitfield'=>0,
				'bbcode_uid'=>0,
				'to_address'=>'',
				'bcc_address'=>'',
			);
			$approve['address_list']['u'][$poster_id]='to';
			if(isset($approve['address_list']['u']))
			{
				include_once($phpbb_root_path.'includes/functions_privmsgs.'.$phpEx);
				submit_pm('post', $user->lang['TORRENT_NOTIFY_SUBJECT'], $approve, false);
			}
		}
		$row['post_edit_locked']=$forb_locked;
		$topic_data['topic_status']=$forb_closed;
		$torrents[$torrent_id]['forb_reason']=$forb_reason;
		$torrents[$torrent_id]['forb_user_id']=$user->data['user_id'];
		$torrents[$torrent_id]['username']=$user->data['username'];
		$torrents[$torrent_id]['user_colour']=$user->data['user_colour'];
		$torrents[$torrent_id]['forb_date']=$dt;
		$torrents[$torrent_id]['forb']=$forb_val;
		$torrent_link=$forb_val > 0 ? ($user->data['user_id']==$poster_id && in_array($forb_val, $config['ppkbb_tcauthor_candown']) ? 1 : 0) : 1;
		trigger_error($user->lang['TORR_FORB_CHANGED'].'<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' .$viewtopic_url. '">', '</a>'));
	}
	if($is_cansetforb)
	{
		$form_forb='';
		ksort($torrent_statuses['TRACKER_FORB_REASON']);
		$forb_sel=array();
		foreach($torrent_statuses['TRACKER_FORB_REASON'] as $rk => $rv)
		{
			if($rk < 0 && $rk > -50 && @!$forb_sel[-1])
			{
				$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_MREASON'].'</option>';
				$forb_sel[-1]=1;
			}
			if($rk > 0 && @!$forb_sel[1])
			{
				$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_PREASON'].'</option>';
				$forb_sel[1]=1;
			}
			if($rk == 0 && @!$forb_sel[0])
			{
				$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_UREASON'].'</option>';
				$forb_sel[0]=1;
			}
			$form_forb.='<option value="'.$rk.'"'.($torrents[$torrent_id]['forb']==$rk ? ' selected="selected"' : '').((!$is_cansetforb && ($rk > -50 || $torrents[$torrent_id]['forb'] > 0)) ? ' disabled="disabled"' : '').'>'.$rv.'</option>';
		}
		$torrent_action['TA_FORB_FORM_REASON']=$torrents[$torrent_id]['forb_reason'];
		$torrent_action['TA_FORB_FORM_LOCKED']=$row['post_edit_locked'] ? ' checked="checked"' : '';
		$torrent_action['TA_FORB_FORM_STATUS']=$topic_data['topic_status'] ? ' checked="checked"' : '';
		$torrent_action['TA_FORB_FORM']=$form_forb;
	}
}

if(sizeof($torrents_hashes))
{
	$r_torr=array('all'=>array(), 'torr'=>array());

	$r_exs=array();
	$torrents_id=array_keys($torrents_hashes);

	$sql='SELECT id tracker, torrent FROM '.TRACKER_RTRACK_TABLE." rt WHERE rt.rtrack_enabled='1' AND ((rt.zone_id='0' AND rt.rtrack_remote='1' AND rt.torrent='0') OR rt.torrent IN('".(implode("', '", $torrents_id))."'))";
	$result=$db->sql_query($sql);
	$ra=array();
	while($row_remote=$db->sql_fetchrow($result))
	{
		$ra[$row_remote['tracker']]=$row_remote['torrent'];
		if(!$row_remote['torrent'])
		{
			$r_torr['all'][$row_remote['tracker']]=$torrents_hashes;
		}
		else
		{
			isset($torrents_hashes[$row_remote['torrent']]) ? $r_torr['torr'][$row_remote['tracker']][$row_remote['torrent']]=$torrents_hashes[$row_remote['torrent']] : '';
		}
	}
	$db->sql_freeresult($result);

	$sql='SELECT tracker, torrent, next_announce FROM '.TRACKER_RANNOUNCES_TABLE." WHERE torrent IN('".(implode("', '", $torrents_id))."')";
	$result=$db->sql_query($sql);
	while($row_remote=$db->sql_fetchrow($result))
	{
		if(isset($ra[$row_remote['tracker']]))
		{
			$r_exs[$ra[$row_remote['tracker']].'_'.$row_remote['torrent']][$row_remote['tracker']]=$row_remote;
		}
	}
	$db->sql_freeresult($result);

	if(isset($r_torr['all']))
	{
		foreach($r_torr['all'] as $tr_id => $a_data)
		{
			foreach($a_data as $t_id => $t_hash)
			{
				if(isset($r_exs['0_'.$t_id][$tr_id]))
				{
					if($dt > $r_exs['0_'.$t_id][$tr_id]['next_announce'])
					{
						$torrents_remote[$t_id]=$torrents_hashes[$t_id];
					}
				}
				else
				{
					$torrents_remote[$t_id]=$torrents_hashes[$t_id];
				}
			}
		}
		unset($r_torr['all']);
	}
	if(isset($r_torr['torr']))
	{
		foreach($r_torr['torr'] as $tr_id => $a_data)
		{
			foreach($a_data as $t_id => $t_hash)
			{
				if(isset($r_exs[$t_id.'_'.$t_id][$tr_id]))
				{
					if($dt > $r_exs[$t_id.'_'.$t_id][$tr_id]['next_announce'])
					{
						$torrents_remote[$t_id]=$torrents_hashes[$t_id];
					}
				}
				else
				{
					$torrents_remote[$t_id]=$torrents_hashes[$t_id];
				}
			}
		}
		unset($r_torr['torr']);
	}
	unset($torrents_hashes);
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

$torrent_stat['TS_AUTHOR']=($poster_id != ANONYMOUS) ? $user_cache[$poster_id]['author_full'] : get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
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

if($is_candowntorr && $config['ppkbb_portal_torrents_posttime'][0] >= 0 && $is_cansetsticky && $torrents[$torrent_id]['forb'] < 1)
{
	$set_sticky=request_var('set_sticky', 0);
	if($set_sticky)
	{
		$as_sticky=request_var('as_sticky', 0);
		$tlimit_sticky=request_var('torrent_time_limit', 0);
		$tlimit_sticky > 999 || $tlimit_sticky < 0 ? $tlimit_sticky=0 : $tlimit_sticky=$tlimit_sticky * 86400;
		if($as_sticky && $torrent_link)
		{
			$topic_data['topic_type']=POST_STICKY;
		}
		else
		{
			$topic_data['topic_type']=POST_NORMAL;
			$tlimit_sticky=0;
		}
		$topic_data['topic_time_limit']=$tlimit_sticky;
		$result=$db->sql_query('UPDATE '. TOPICS_TABLE ." SET topic_time_limit='".$tlimit_sticky."', topic_type='".$topic_data['topic_type']."' WHERE topic_id='{$topic_data['topic_id']}'");
		trigger_error($user->lang['TORR_STICKY_SET'].'<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' .$viewtopic_url. '">', '</a>'));
	}
	$left_sticky=0;
	if($topic_data['topic_time_limit'] && ($topic_data['topic_time'] + $topic_data['topic_time_limit']) > $dt)
	{
		$left_sticky=$td->spelldelta($dt, $topic_data['topic_time'] + $topic_data['topic_time_limit']);
	}
	//$torrent_action['TA_STICKY_FORM']='<form action="'. $viewtopic_url .'" method="post">'.$user->lang['TORRENT_INFO_HEADER_SETSTICKY'].'<br /><input type="submit" name="set_sticky" class="button1" value="'.$user->lang['TORRENT_ACTION'].'" /> <input type="text" name="torrent_time_limit" id="topic_time_limit" size="3" maxlength="3" value="'.($topic_data['topic_time_limit'] / 86400).'" class="inputbox autowidth" title="'.$user->lang['STICKY_DESCR'].'" />&nbsp;<input type="checkbox" name="as_sticky" value="1" title="'.$user->lang['TORRENT_INFO_HEADER_STICKY'].'"'.($topic_data['topic_type']==POST_STICKY ? ' checked="checked"' : '').' />'.($left_sticky ? '&nbsp;'.$left_sticky : '').'</form>';
	$torrent_action['TA_STICKY_FORM']=true;
	$torrent_action['TA_STICKY_FORM_LEFT_DAYS']=$left_sticky ? $topic_data['topic_time_limit'] / 86400 : '';
	$torrent_action['TA_STICKY_FORM_STICKY']=$topic_data['topic_type']==POST_STICKY ? ' checked="checked"' : '';
	$torrent_action['TA_STICKY_FORM_LEFT']=$left_sticky ? $left_sticky : false;
}

$dthanks=0;
if($is_candowntorr && $config['ppkbb_thanks_enable'] && !$torrents[$torrent_id]['thank'] && $torrents[$torrent_id]['poster_id']!=$user->data['user_id'] && $is_canusethanks && $torrent_link)
{
	$say_thanks=request_var('say_thanks', 0);
	!$say_thanks ? $say_thanks=(request_var('submit_thanks_x', '') || request_var('submit_thanks_y', '')) : '';
	if($say_thanks)
	{
		$db->sql_query('INSERT IGNORE INTO '. TRACKER_THANKS_TABLE ." (user_id, torrent_id, to_user, tadded, post_id) VALUES ('{$user->data['user_id']}', '{$torrent_id}', '{$torrents[$torrent_id]['poster_id']}', '{$dt}', '{$torrents[$torrent_id]['post_msg_id']}')");
		if($db->sql_affectedrows())
		{
			$db->sql_query("UPDATE ".USERS_TABLE." SET user_tothanks_count=user_tothanks_count+1 WHERE user_id='{$torrents[$torrent_id]['poster_id']}'");
			$db->sql_query("UPDATE ".USERS_TABLE." SET user_fromthanks_count=user_fromthanks_count+1 WHERE user_id='{$user->data['user_id']}'");
			$dthanks=1;
			trigger_error($user->lang['TORR_THANKS_SEND'].'<br /><br />' . sprintf($user->lang['RETURN_TOPIC'], '<a href="' .$viewtopic_url. '">', '</a>'));
		}
	}
	!$dthanks ? $torrent_action['TA_THANKS_FORM']=true : false;
}

$i3=0;
$viewtopic_add1inc=$phpbb_root_path.'tracker/include/';
!$config['ppkbb_mua_countlist'] ? !$config['ppkbb_mua_countlist']=array(5, 10, 25, 50, 75, 100, 250, 500, 750, 1000, 5000, 10000) : $config['ppkbb_mua_countlist']=my_split_config($config['ppkbb_mua_countlist'], 0, 'my_int_val');
sort($config['ppkbb_mua_countlist']);
$mua_limit=$config['ppkbb_mua_countlist'][sizeof($config['ppkbb_mua_countlist'])-1];
switch($torrent_opt)
{
	case 'leave':
		include($viewtopic_add1inc.'viewtopic_add1_leave.'.$phpEx);
		break;

	case 'history':
		include($viewtopic_add1inc.'viewtopic_add1_history.'.$phpEx);
		break;

	case 'filelist':
		include($viewtopic_add1inc.'viewtopic_add1_filelist.'.$phpEx);
		break;

	case 'finished':
		include($viewtopic_add1inc.'viewtopic_add1_finished.'.$phpEx);
		break;

	case 'seed':
		include($viewtopic_add1inc.'viewtopic_add1_seed.'.$phpEx);
		break;

	case 'leech':
		include($viewtopic_add1inc.'viewtopic_add1_leech.'.$phpEx);
		break;

	case 'thanks':
		include($viewtopic_add1inc.'viewtopic_add1_thanks.'.$phpEx);
		break;

	case 'remote':
		include($viewtopic_add1inc.'viewtopic_add1_remote.'.$phpEx);
		break;

	case 'downloads':
		include($viewtopic_add1inc.'viewtopic_add1_downloads.'.$phpEx);
		break;

	default:
		$torrent_opt='';
		break;
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
	$user->lang['LOGORREG_DOWNLOAD']=sprintf($user->lang['LOGORREG_DOWNLOAD'], reapply_sid("{$phpbb_root_path}ucp.{$phpEx}?mode=register&amp;redirect=".build_url()), reapply_sid("{$phpbb_root_path}ucp.{$phpEx}?mode=login&amp;redirect=".build_url()));
}

$torrent_info_curr_explain[$opt]=true;
$postrow=array_merge($postrow, array(

	'S_HAS_TRACKER_TORRENT' => (isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']) && /*$torrent_guest && */$config['ppkbb_torr_blocks'][1]) ? true : false,
	'S_HAS_TRACKER_POSTER' => (isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']) && $config['ppkbb_torr_blocks'][2]) ? true : false,
	'S_HAS_TRACKER_SCREENSHOT' => (isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']) && $config['ppkbb_torr_blocks'][3]) ? true : false,
	'TRACKER_TORRENTS_COUNT' => isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']) > 1 ? true : false,
	'TRACKER_POSTERS_COUNT' => isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']) > 1 ? true : false,
	'TRACKER_SCREENSHOTS_COUNT' => isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']) > 1 ? true : false,

	'TORRENT_INFO_STAT' => $torrent_stat && $config['ppkbb_torr_blocks'][0] ? true : false,
	'TORRENT_INFO_ACTION' => $torrent_action && $config['ppkbb_torr_blocks'][4] ? true : false,
	'TORRENT_INFO_OPT' => $is_candowntorr && sizeof($torrent_info) ? implode(' : ', $torrent_info) : '',
	'TORRENT_INFO_AUTHOR' => $config['ppkbb_torr_blocks'][8] ? true : false,

	'S_TORRENT_TOP_NOTICE' => $torrent_top_notice,
	'S_TORRENT_BOTTOM_NOTICE' => $torrent_bottom_notice,

	'S_HAS_TORRENT_EXPLAIN'	=> $torrent_info_curr ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FILELIST'	=> @$torrent_info_curr_explain['filelist'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FINISHED'	=> @$torrent_info_curr_explain['finished'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_THANKS'	=> @$torrent_info_curr_explain['thanks'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_HISTORY'	=> @$torrent_info_curr_explain['history'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEAVE'	=> @$torrent_info_curr_explain['leave'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_SEED'	=> @$torrent_info_curr_explain['seed'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEECH'	=> @$torrent_info_curr_explain['leech'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_REMOTE'	=> $config['ppkbb_tcenable_rannounces'][0] && @$torrent_info_curr_explain['remote'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_DOWNLOADS'	=> @$torrent_info_curr_explain['downloads'] ? true : false,

	'FORUM_IMAGE' => $forum_image ? $phpbb_root_path.$forum_image : '',

	'S_VIEWTOPIC_URL' => $viewtopic_url,
	'S_IS_ADMOD' => $is_admod ? true : false,

	'S_MUA_COUNTLIST_DEFAULT' => $config['ppkbb_mua_countlist'][0],
	'S_MUA_COUNTLIST' => implode(', ', $config['ppkbb_mua_countlist']),
));

//if($config['ppkbb_torr_blocks'][0])
//{
	if(sizeof($torrent_stat))
	{
		$torrent_stats=array();
		foreach($torrent_stat as $sk => $sv)
		{
			$torrent_stats[$sk]=$sv;
		}
		$postrow=array_merge($postrow, $torrent_stats);
	}
	if(sizeof($torrent_action))
	{
		$postrow=array_merge($postrow, $torrent_action);
	}
//}
?>
