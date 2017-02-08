<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_users_add_tracker.php 1.000 2009-06-23 11:06:00 PPK $
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

include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

if(!class_exists('timedelta'))
{
$user->add_lang('mods/posts_merging');
require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
}
$td = new timedelta();
$data = array(
	'uploaded'			=> my_int_val(request_var('tr_up', $user_row['user_uploaded'])),
	'uploaded_self'			=> my_int_val(request_var('tr_up_self', $user_row['user_uploaded_self'])),
	'downloaded'			=> my_int_val(request_var('tr_down', $user_row['user_downloaded'])),
	'shadow_downloaded'	=> my_int_val(request_var('tr_shadow_down', $user_row['user_shadow_downloaded'])),
	'comments'			=> my_int_val(request_var('tr_comm', $user_row['user_comments'])),
	'torrents'				=> my_int_val(request_var('tr_torr', $user_row['user_torrents'])),
	'bonus'				=> my_float_val(request_var('tr_bonus', $user_row['user_bonus'])),
	'passkey'				=> request_var('tr_passkey', $user_row['user_passkey']),
	'tothanks'				=> my_int_val(request_var('tr_tothanks', $user_row['user_tothanks_count'])),
	'fromthanks'			=> my_int_val(request_var('tr_fromthanks', $user_row['user_fromthanks_count'])),
);

$dt=time();

if ($submit)
{
	$data['uploaded']			= get_size_value(request_var('tr_upv', 'b'), $data['uploaded']);
	$data['uploaded_self']			= get_size_value(request_var('tr_up_selfv', 'b'), $data['uploaded_self']);
	$data['downloaded']			= get_size_value(request_var('tr_downv', 'b'), $data['downloaded']);
	$data['shadow_downloaded']	= get_size_value(request_var('tr_shadow_downv', 'b'), $data['shadow_downloaded']);
	if(request_var('reset_ratio', 0))
	{
		$sql="UPDATE ".USERS_TABLE." SET user_uploaded='0', user_downloaded='0', user_uploaded_self='0' WHERE user_id='{$user_id}'";
		$db->sql_query($sql);
		$data['uploaded']=$data['downloaded']=$data['uploaded_self']=0;
	}
	if(request_var('reset_bonus', 0))
	{
		$sql="UPDATE ".USERS_TABLE." SET user_bonus='0.000' WHERE user_id='{$user_id}'";
		$db->sql_query($sql);
		$data['bonus']=0.000;
	}

	if(request_var('clear_trights', 0) && !request_var('clear_peers', 0))
	{
		$sql="UPDATE ".TRACKER_PEERS_TABLE." SET rights='' WHERE userid='{$user_id}'";
		$db->sql_query($sql);
	}
	if(request_var('clear_snatch', 0))
	{
		$sql="SELECT tt.id FROM ".TRACKER_TORRENTS_TABLE." tt LEFT JOIN ".TOPICS_TABLE." t ON (tt.poster_id=t.topic_poster AND tt.post_msg_id=t.topic_first_post_id) WHERE tt.poster_id='{$user_id}' AND ISNULL(t.topic_first_post_id) AND tt.unreg='0'";
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

			$sql="DELETE FROM ".TRACKER_TORRENTS_TABLE." WHERE id IN('{$t_clean}')";
			$db->sql_query($sql);

			$sql="DELETE FROM ".TRACKER_THANKS_TABLE." WHERE torrent_id IN('{$t_clean}')";
			$db->sql_query($sql);

			$sql="DELETE FROM ".TRACKER_FILES_TABLE." WHERE id IN('{$t_clean}')";
			$db->sql_query($sql);

			$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE torrent IN('{$t_clean}')";
			$db->sql_query($sql);

			$sql="DELETE FROM ".TRACKER_RANNOUNCES_TABLE." WHERE torrent IN('{$t_clean}')";
			$db->sql_query($sql);
		}

		$sql="SELECT s.id FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (s.torrent=t.id) WHERE s.userid='{$user_id}' AND ISNULL(t.id)";
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

		$sql="SELECT t.id FROM ".TRACKER_THANKS_TABLE." t LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (t.post_id=tt.post_msg_id) WHERE t.user_id='{$user_id}' AND ISNULL(tt.id)";
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

			$sql="DELETE FROM ".TRACKER_THANKS_TABLE." WHERE id IN('{$t_clean3}')";
			$db->sql_query($sql);
		}

		$sql="SELECT p.id FROM ".TRACKER_PEERS_TABLE." p LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (p.torrent=t.id) WHERE p.userid='{$user_id}' AND ISNULL(t.id)";
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

		$sql="SELECT f.id FROM ".TRACKER_FILES_TABLE." f LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (f.id=tt.id) WHERE ISNULL(tt.id)";
		$result=$db->sql_query($sql);
		$t_clean5=array();
		while($row=$db->sql_fetchrow($result))
		{
			$t_clean5[]=$row['id'];
		}
		$db->sql_freeresult($result);
		if(sizeof($t_clean5))
		{
			$t_clean5=implode("', '", $t_clean5);

			$sql="DELETE FROM ".TRACKER_FILES_TABLE." WHERE id IN('{$t_clean5}')";
			$db->sql_query($sql);
		}

	}
	if(request_var('clear_peers', '')=='all')
	{
		$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE userid='{$user_id}'";
		$db->sql_query($sql);
	}
	else if(request_var('clear_peers', '')=='time')
	{
		$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE userid='{$user_id}' AND last_action < ".($dt-$config['ppkbb_tcdead_time'])."";
		$db->sql_query($sql);
	}
	if(request_var('chat_kick', 0))
	{
		$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE user_id='{$user_id}'";
		$db->sql_query($sql);
	}
	if (request_var('recreate_passkey', 0) && ($npk=create_passkey($user_id)))
	{
		$data['passkey']=$npk;
	}
	if(request_var('collect_tbonus', 0))
	{
		if($config['ppkbb_tcbonus_fsize'][1])
		{
			$sql = 'SELECT SUM(bonus_count) bonus_count, MAX(id) id FROM '.TRACKER_SNATCHED_TABLE." WHERE userid='{$user_id}'";
			$result=$db->sql_query($sql);
			$user_bonus=$db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$bonus_value = $user_bonus['bonus_count'];
			$bonus_value = intval($bonus_value / $config['ppkbb_tcbonus_fsize'][1]);

			if($bonus_value > 0)
			{
				$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='0' WHERE userid='{$user_id}'";
				$result=$db->sql_query($sql);
				$bonus_left = intval($user_bonus['bonus_count'] - ($bonus_value * $config['ppkbb_tcbonus_fsize'][1]));
				$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='".($bonus_left > 0 ? $bonus_left : 0)."' WHERE id='{$user_bonus['id']}' AND userid='{$user_id}' LIMIT 1";
				$result=$db->sql_query($sql);
				$sql = 'UPDATE '.USERS_TABLE.' SET user_bonus=user_bonus+'.($bonus_value * $config['ppkbb_tcbonus_value'][3])." WHERE user_id='{$user_id}'";
				$result=$db->sql_query($sql);
			}
		}
		else
		{
				$sql = 'SELECT s.bonus_count, s.id, tt.size FROM '.TRACKER_SNATCHED_TABLE.' s, '.TRACKER_TORRENTS_TABLE." tt WHERE s.torrent=tt.id AND s.userid='{$user_id}' AND s.bonus_count>0";
				$result=$db->sql_query($sql);
				$coll_bonus=0;
				$bonus_sum=0;
				while($row=$db->sql_fetchrow($result))
				{
					if($row['bonus_count'] >= $row['size'])
					{
						$bonus_value = $row['bonus_count'];
						$bonus_value = intval($bonus_value / $row['size']);
						$bonus_sum+=$row['bonus_count'];

						if($bonus_value > 0)
						{
							$bonus_left = intval($row['bonus_count'] - ($bonus_value * $row['size']));
							$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='".($bonus_left > 0 ? $bonus_left : 0)."' WHERE id='{$row['id']}' AND userid='{$user_id}' LIMIT 1";
							$result=$db->sql_query($sql);
						$coll_bonus+=$bonus_value * $config['ppkbb_tcbonus_value'][3];
						}
					}
				}
				$db->sql_freeresult($result);
				if($coll_bonus)
				{
					$sql = 'UPDATE '.USERS_TABLE." SET user_bonus=user_bonus+'{$coll_bonus}' WHERE user_id='{$user_id}'";
					$result=$db->sql_query($sql);
				}
		}
		$sql = 'SELECT user_bonus FROM '.USERS_TABLE." WHERE user_id='{$user_id}'";
		$result=$db->sql_query($sql);
		$user_bonus=$db->sql_fetchfield('user_bonus');
		$data['bonus']=my_float_val($user_bonus);
	}
	$data['uploaded']			= substr($data['uploaded'], 0, 20);
	$data['uploaded_self']			= substr($data['uploaded_self'], 0, 20);
	$data['downloaded']			= substr($data['downloaded'], 0, 20);
	$data['shadow_downloaded'] = substr($data['shadow_downloaded'], 0, 20);

	$data['comments']			= substr($data['comments'], 0, 8);
	$data['torrents']			= substr($data['torrents'], 0, 8);
	$data['bonus']			= substr($data['bonus'], 0, 7);
	$data['passkey']			= substr($data['passkey'], 0, 32);
	$data['tothanks']			= my_int_val($data['tothanks']);
	$data['fromthanks']			= my_int_val($data['fromthanks']);

	if($data['passkey'] && !request_var('recreate_passkey', 0))
	{
		$sql="SELECT username FROM ".USERS_TABLE." WHERE user_id!='{$user_id}' AND user_passkey='".$db->sql_escape($data['passkey'])."' LIMIT 1";
		$result=$db->sql_query($sql);
		$data2=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if($data2['username'])
		{
			$error[] = $user->lang['DUPLICATED_PASSKEY'];
		}
	}
	if (!check_form_key($form_name))
	{
		$error[] = $user->lang['FORM_INVALID'];
	}

	if (!sizeof($error))
	{
		$sql_ary = array(
			'user_uploaded'			=> $data['uploaded'],
			'user_uploaded_self'			=> $data['uploaded_self'],
			'user_downloaded'			=> $data['downloaded'],
			'user_shadow_downloaded'		=> $data['shadow_downloaded'],
			'user_comments'	=> $data['comments'],
			'user_torrents'	=> $data['torrents'],
			'user_bonus'	=> $data['bonus'],
			'user_passkey'	=> $data['passkey'],
			'user_fromthanks_count' => $data['fromthanks'],
			'user_tothanks_count' => $data['tothanks'],
		);

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
			WHERE user_id = $user_id";
		$db->sql_query($sql);

		trigger_error($user->lang['USER_PREFS_UPDATED'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
	}
	else
	{
		$errors = implode('<br />', $error);
		trigger_error($errors . adm_back_link($this->u_action . '&amp;u=' . $user_id));
	}
}

$template->assign_vars(array(
	'TR_UP'		=> $data['uploaded'],
	'TR_UP_SELF'		=> $data['uploaded_self'],
	'TR_DOWN'		=> $data['downloaded'],
	'TR_SHADOW_DOWN'			=> $data['shadow_downloaded'],
	'TR_HUP'		=> get_formatted_filesize($data['uploaded']),
	'TR_HUP_SELF'		=> get_formatted_filesize($data['uploaded_self']),
	'TR_HDOWN'		=> get_formatted_filesize($data['downloaded']),
	'TR_HSHDOWN'		=> get_formatted_filesize($data['shadow_downloaded']),

	'TR_COMM'			=> $data['comments'],
	'TR_TORR'		=> $data['torrents'],
	'TR_BONUS'		=> $data['bonus'],
	'TR_PASSKEY'			=> $data['passkey'],
	'TR_RATIO'		=> get_ratio_alias(get_ratio($data['uploaded'], $data['downloaded'], $config['ppkbb_tcratio_start'], $data['bonus'])),
	'TR_RRATIO'		=> get_ratio_alias(get_ratio($data['uploaded'], $data['shadow_downloaded'])),
	'TR_TOTHANKS'		=> $data['tothanks'],
	'TR_FROMTHANKS'		=> $data['fromthanks'],

	'S_TRACKER'		=> true,
	)
);

?>
