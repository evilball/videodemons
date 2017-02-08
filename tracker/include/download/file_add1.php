<?php
/**
*
* @package ppkBB3cker
* @version $Id: file_add1.php 1.000 2009-05-14 16:09:00 PPK $
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

$sql = 'SELECT * FROM ' . TRACKER_TORRENTS_TABLE . " t
	WHERE t.post_msg_id = '" . $attachment['post_msg_id']."'";
$result = $db->sql_query_limit($sql, 1);
$torrents = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
if(!$user->data['is_registered'])
{
	$is_candowntorr = $auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $row['forum_id']) ? 1 : 0;
	$torrents && $config['ppkbb_tcguests_enabled'][0] && $config['ppkbb_tctrestricts_options'][2] && $torrents['size'] < $config['ppkbb_tctrestricts_options'][2] ? $is_candowntorr=1 : '';
}
else
{
	$is_candowntorr=1;
	if($user->data['user_id']!=$attachment['poster_id'])
	{
		$is_candowntorr=$auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $row['forum_id']) ? 1 : 0;
	}
}

if(!$is_candowntorr)
{
	trigger_error('CANT_DOWN_TORRENT');
}


if($torrents)
{
	$torrent_statuses=get_torrent_statuses();
	if(isset($torrent_statuses['TRACKER_FORB_MARK'][$torrents['forb']]))
	{
		$torrents['forb'] < 1 || ($torrents['forb'] > 0 && $user->data['user_id']==$attachment['poster_id'] && in_array($torrents['forb'], $config['ppkbb_tcauthor_candown'])) ? '' : trigger_error(sprintf($user->lang['FORM_TORRENT_FORB'], $torrent_statuses['TRACKER_FORB_REASON'][$torrents['forb']]));
		if(!$user->data['is_registered'] && $torrents['forb'] < 1 && in_array($torrents['forb'], $config['ppkbb_tcguest_cantdown']))
		{
			$register_link=append_sid("{$phpbb_root_path}ucp.{$phpEx}", "mode=register", false);
			$login_link=append_sid("{$phpbb_root_path}ucp.{$phpEx}", "mode=login", false);

			trigger_error(sprintf($user->lang['LOGORREG_DOWNLOAD'], $register_link, $login_link));

		}
	}

	$is_canskiprcheck = $user->data['is_registered'] && $user->data['user_id']!=$attachment['poster_id'] ? (($auth->acl_get('u_canskiprcheck') && $auth->acl_get('f_canskiprcheck', $row['forum_id'])) ? 1 : 0) : 1;
	$is_canskiprequpload = $user->data['is_registered'] && $user->data['user_id']!=$attachment['poster_id'] ? (($auth->acl_get('u_canskiprequpload') && $auth->acl_get('f_canskiprequpload', $row['forum_id'])) ? 1 : 0) : 1;
	$is_canskipreqratio = $user->data['is_registered'] && $user->data['user_id']!=$attachment['poster_id'] ? (($auth->acl_get('u_canskipreqratio') && $auth->acl_get('f_canskipreqratio', $row['forum_id'])) ? 1 : 0) : 1;

	$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);

	$t_elapsed = intval((time() - $torrents['added']) / 3600);
	$t_wait=-1;
	if(!$is_canskiprcheck && $config['ppkbb_tcwait_time'])
	{
		$t_wait=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time'], 'up');

		if ($t_wait > 0)
		{
			$t_elapsed < $t_wait ? $t_wait=$t_wait - $t_elapsed : $t_wait=-1;
		}
		if($t_wait >= 0)
		{
			trigger_error(($t_wait > 0) ? sprintf($user->lang['TORRENT_WAIT'], $t_wait) : $user->lang['TORRENT_WAIT_NEVER']);
		}
	}
	$t_wait2=-1;
	if(!$is_canskiprcheck && $config['ppkbb_tcwait_time2'])
	{
		$t_wait2=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time2']);
		if ($t_wait2 > 0)
		{
			$t_elapsed < $t_wait2 ? $t_wait2=$t_wait2 - $t_elapsed : $t_wait2=-1;
		}
		if($t_wait2 >= 0)
		{
			trigger_error(($t_wait2 > 0) ? sprintf($user->lang['TORRENT_WAIT'], $t_wait2) : $user->lang['TORRENT_WAIT_NEVER']);
		}
	}
	if(!$is_canskiprequpload && $torrents['req_upload'])
	{
		if($user->data['user_uploaded'] < $torrents['req_upload'])
		{
			trigger_error(sprintf($user->lang['TORRENT_REQ_UPLOAD_1'], get_formatted_filesize($torrents['req_upload']), get_formatted_filesize($user->data['user_uploaded'])));
		}
	}
	if(!$is_canskipreqratio && $torrents['req_ratio']!=0.000)
	{
		if($user_ratio!='None.' && ($user_ratio < $torrents['req_ratio'] || $user_ratio=='Leech.' || $user_ratio=='Inf.'))
		{
			trigger_error(sprintf($user->lang['TORRENT_REQ_RATIO_1'], $torrents['req_ratio'], get_ratio_alias($user_ratio)));
		}
	}
}
else
{
	$forum_astracker=0;
}
?>
