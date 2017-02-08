<?php
/**
*
* @package ppkBB3cker
* @version $Id: ucp_prefs_add1_tracker_details.php 1.000 2009-08-19 12:18:00 PPK $
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

add_form_key('ucp_tracker');

$dt=time();

if(!class_exists('timedelta'))
{
$user->add_lang('mods/posts_merging');
require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
}
$td = new timedelta();

$template->assign_vars(array(
	'S_TRBONUS_ENABLED'	=> $config['ppkbb_tcbonus_value'][0] > 0 ? true : false,

	'S_UNREGTORR_ENABLED' => $config['ppkbb_tcallow_unregtorr'] ? generate_board_url()."/tracker/announce.{$phpEx}?passkey={$user->data['user_passkey']}" : false,
	'S_UTO_PORTAL' => $user->data['user_tracker_options'][0] ? true : false,
	'S_UTO_CHAT' => $user->data['user_tracker_options'][1] ? true : false,
	'S_UTO_TDT' => $user->data['user_tracker_options'][2] ? true : false,
	'S_TDT_TYPE' => $config['ppkbb_topdown_torrents'][11] ? false : true,
	)
);

if($submit)
{
	$message='';
	if (check_form_key('ucp_tracker'))
	{

		if(request_var('reset_bonus', 0) && !request_var('reset_ratio', 0))
		{
			$sql='UPDATE '.USERS_TABLE." SET user_bonus='0.000' WHERE user_id='{$user->data['user_id']}'";
			$db->sql_query($sql);

			meta_refresh(3, $this->u_action);
			$message .= $user->lang['USER_TBONUS_RESETTED'] . '<br /><br />';
		}

		if(request_var('recreate_passkey', 0) && create_passkey())
		{
			meta_refresh(3, $this->u_action);
			$message = $user->lang['USER_PASSKEY_CREATED'] . '<br />';
		}
		if(request_var('collect_tbonus', 0) && $config['ppkbb_tcbonus_value'][0] > 0)
		{
			$user_bonus_max=999.999;
			if($config['ppkbb_tcbonus_fsize'][1])
			{
				$sql = 'SELECT SUM(bonus_count) bonus_count, MAX(id) id FROM '.TRACKER_SNATCHED_TABLE." WHERE userid='{$user->data['user_id']}'";
				$result=$db->sql_query($sql);
				$user_bonus=$db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$bonus_value = $user_bonus['bonus_count'];
				$bonus_value = intval($bonus_value / $config['ppkbb_tcbonus_fsize'][1]);

				if($bonus_value > 0)
				{
					if($user->data['user_bonus']+($bonus_value * $config['ppkbb_tcbonus_value'][3])<=$user_bonus_max)
				{
					$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='0' WHERE userid='{$user->data['user_id']}'";
					$result=$db->sql_query($sql);

						$bonus_left = my_int_val($user_bonus['bonus_count'] - ($bonus_value * $config['ppkbb_tcbonus_fsize'][1]));
					$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='".($bonus_left > 0 ? $bonus_left : 0)."' WHERE id='{$user_bonus['id']}' AND userid='{$user->data['user_id']}' LIMIT 1";
					$result=$db->sql_query($sql);

						$sql = 'UPDATE '.USERS_TABLE.' SET user_bonus=user_bonus+'.($bonus_value * $config['ppkbb_tcbonus_value'][3])." WHERE user_id='{$user->data['user_id']}'";
					$result=$db->sql_query($sql);
					meta_refresh(3, $this->u_action);
						$message .= sprintf($user->lang['USER_TBONUS_COLLECTED'], ($bonus_value * $config['ppkbb_tcbonus_value'][3]), get_formatted_filesize($user_bonus['bonus_count'])) . '<br />';
				}
				else
				{
					meta_refresh(3, $this->u_action);
						$message .= sprintf($user->lang['USER_TBONUS_MAXIMUM'], $user_bonus_max) . '<br />';
					}
				}
				else
				{
					meta_refresh(3, $this->u_action);
					$message .= sprintf($user->lang['USER_TBONUS_COLLECTED'], 0, get_formatted_filesize($user_bonus['bonus_count'])) . '<br />';
				}
			}
			else
			{
				$sql = 'SELECT s.bonus_count, s.id, tt.size FROM '.TRACKER_SNATCHED_TABLE.' s, '.TRACKER_TORRENTS_TABLE." tt WHERE s.torrent=tt.id AND s.userid='{$user->data['user_id']}' AND s.bonus_count>0";
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
							$sql = 'UPDATE '.TRACKER_SNATCHED_TABLE." SET bonus_count='".($bonus_left > 0 ? $bonus_left : 0)."' WHERE id='{$row['id']}' AND userid='{$user->data['user_id']}' LIMIT 1";
							$result=$db->sql_query($sql);
							$coll_bonus+=$bonus_value * $config['ppkbb_tcbonus_value'][3];
						}
					}
				}
				$db->sql_freeresult($result);
				if($coll_bonus)
				{
					if($user->data['user_bonus']+$coll_bonus<=$user_bonus_max)
				{
					$sql = 'UPDATE '.USERS_TABLE." SET user_bonus=user_bonus+'{$coll_bonus}' WHERE user_id='{$user->data['user_id']}'";
					$result=$db->sql_query($sql);
					meta_refresh(3, $this->u_action);
					$message .= sprintf($user->lang['USER_TBONUS_COLLECTED'], $coll_bonus, get_formatted_filesize($bonus_sum)) . '<br />';
				}
				else
				{
					meta_refresh(3, $this->u_action);
						$message .= sprintf($user->lang['USER_TBONUS_MAXIMUM'], $user_bonus_max) . '<br />';
					}
				}
				else
				{
					meta_refresh(3, $this->u_action);
					$message .= sprintf($user->lang['USER_TBONUS_COLLECTED'], 0, get_formatted_filesize($bonus_sum)) . '<br />';
				}
			}
		}

		if(request_var('clear_trights', 0) && !request_var('clear_peers', 0))
		{
			$sql='UPDATE '.TRACKER_PEERS_TABLE." SET rights='' WHERE userid='{$user->data['user_id']}'";
			$db->sql_query($sql);

			meta_refresh(3, $this->u_action);
			$message .= $user->lang['USER_TRIGHTS_CLEARED'] . '<br /><br />';

		}
		if(request_var('clear_peers', '')=='all')
		{
			$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE userid='{$user->data['user_id']}'";
			$db->sql_query($sql);

			meta_refresh(3, $this->u_action);
			$message .= $user->lang['USER_TPEERS_CLEARED'] . '<br /><br />';
		}
		else if(request_var('clear_peers', '')=='time')
		{
			$sql="DELETE FROM ".TRACKER_PEERS_TABLE." WHERE userid='{$user->data['user_id']}' AND last_action < ".($dt-$config['ppkbb_tcdead_time'])."";
			$db->sql_query($sql);

			meta_refresh(3, $this->u_action);
			$message .= $user->lang['USER_TPEERS_CLEARED'] . '<br /><br />';
		}
		$uto_portal=request_var('uto_portal', 0) ? 1 : 0;
		$uto_chat=request_var('uto_chat', 0) ? 1 : 0;
		$uto_tdt=request_var('uto_tdt', 0) ? 1 : 0;
		$sql = 'UPDATE '.USERS_TABLE." SET user_tracker_options='{$uto_portal} {$uto_chat} {$uto_tdt}' WHERE user_id='{$user->data['user_id']}'";
		$db->sql_query($sql);
		$message .= $user->lang['RTRACK_SUCCESS'] . '<br /><br />';
		$message ? trigger_error($message. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>')) : '';
	}
	else
	{
		$error[] = 'FORM_INVALID';
	}
	// Replace "error" strings with their real, localised form
	$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
}

$template->assign_vars(array(
	'ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : '',
));
?>
