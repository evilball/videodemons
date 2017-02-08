<?php
/**
*
* @package ppkBB3cker
* @version $Id: chat.php 1.000 2014-06-19 17:30:23 PPK $
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

define('SHOW_CHAT', true);

if(defined('IN_PORTAL'))
{
	$gen_page="portal.{$phpEx}";
}
else
{
	$gen_page="viewchat.{$phpEx}";

	defined('IN_CHAT') ? $chat_id=$forum_id : '';
}

$user_window=$online_window=$form_window=$bar_window=$bbcode_window=$message_window=$chat_exit=false;
$online_message=$chat_key=$chat_action='';
$users=$del_users=$chat_link=$max_id=array();
$chat_user=0;
$user_ip=sprintf('%u', ip2long($user->ip));
$is_guest=!$user->data['is_registered'] && $config['ppkbb_chat_guests'] && $user->data['user_id']==1 ? 1 : 0;
$user_id=$is_guest ? $user_ip-($user_ip*2) : $user->data['user_id'];
$is_guest ? $chat_key=md5($user_ip) : '';

$username=$user->data['username'];

if(!$chat_alt)
{
	$user->add_lang('mods/ppkbb3cker_chat');

	$query=('SELECT * FROM ' .PPKCHAT_USERS_TABLE. " WHERE room='{$chat_id}'");
	$result=$db->sql_query($query);
	while($row=$db->sql_fetchrow($result))
	{
		if($row['room']==$chat_id)
		{
			if($dt - $row['lastaccess'] < $config['ppkbb_chat_inactive_time'] && $row['chatkey'])
			{

				if($is_guest)
				{
					if($row['chatkey']==$chat_key)
					{
						if($chat && $chat!='exit' && $config['ppkbb_chat_autokill_onrefresh'] && $dt - $row['lastaccess'] < $config['ppkbb_chat_murefresh'] && $dt > $row['lastaccess'])
						{
							trigger_error($user->lang['CHAT_REFRESH']);
						}
						$chat_user=$row['user_id'];
						$chat=='exit' ? '' : $chat='continue';
						$chat_action='exit';
					}
				}
				else
				{
					if($row['user_id']==$user_id && $row['chatkey']==$user->data['user_chatkey'])
					{
						if($chat && $chat!='exit' && $config['ppkbb_chat_autokill_onrefresh'] && $dt - $row['lastaccess'] < $config['ppkbb_chat_murefresh'] && $dt > $row['lastaccess'])
						{
							trigger_error($user->lang['CHAT_REFRESH']);
						}
						$chat_user=$row['user_id'];
						$chat=='exit' ? '' : $chat='continue';
						$chat_action='exit';
						$chat_key=$row['chatkey'];
					}

				}
				$users[$row['user_id']]=$row;
			}
			else
			{
				$del_users[$row['user_id']]=$row['user_id'];
			}
		}
	}
	$db->sql_freeresult($result);

	if($del_users)
	{
		$query=('DELETE FROM ' .PPKCHAT_USERS_TABLE. " WHERE user_id IN(".implode(', ', $del_users).")");
		$result=$db->sql_query($query);
	}

	if($chat=='enter' && $chat_user)
	{
		$chat_action=$chat='exit';
		$chat_user=0;
	}

	if(isset($users[$user_id]['lastaccess']) && $dt < $users[$user_id]['lastaccess'])
	{
		if(!defined('IN_CHAT'))
		{
			$chat='exit';
			$user->lang['CHAT_LOGIN']=sprintf($user->lang['CHAT_BANNED'], date('Y-m-d H:i:s', $users[$user_id]['lastaccess']));
			$chat_link['enter']=false;
		}
		else
		{
			trigger_error(sprintf($user->lang['CHAT_BANNED'], $user->format_date($users[$user_id]['lastaccess'], 'Y-m-d H:i:s')));
		}
	}

	if(($chat=='exit' && $chat_user) || ($chat_user && !$chat))
	{
		$chat_exit=true;

		$query='DELETE FROM ' .PPKCHAT_USERS_TABLE. " WHERE user_id='{$user_id}' AND lastaccess < {$dt} AND room='{$chat_id}'";
		$result=$db->sql_query($query);

		/*$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_chatkey = ''
			WHERE user_id = '" . $user_id ."' LIMIT 1";
		$result=$db->sql_query($sql);*/

		$chat_action='enter';
		unset($users[$user_id]);
		$chat_user=0;
	}

	$chat_window=request_var('w', 0) ? '&amp;w=1' : '';

	$chat_link['exit']=append_sid("{$phpbb_root_path}{$gen_page}", "f={$chat_id}&amp;chat=exit{$chat_window}#chat");
	$chat_link['enter']=append_sid("{$phpbb_root_path}viewchat.{$phpEx}", "f={$chat_id}&amp;chat=enter{$chat_window}#chat");
	//0-$is_adm 1-$is_mod 2-$is_canaddchatm 3-$is_canviewchatu
	//4-$is_canviewchatm 5-$is_candelchatm 6-$is_canskipchatktime
	//7-$is_canhiddeninchat 8-$is_canviewhiddinchat 9-$is_cansendchatpm
	//10-$is_canskipdisbutton 11-$is_canusesmilies 12-$is_canusebbcodes
	//13-$is_candelchatm2 14-$is_canusegmode 15-$is_canuseacommands
	//16-$is_canviewarchive 17-$is_canusebbimg 18-$is_caneditchatm 19-$is_caneditchatm2
	//20-$is_canviewavatars
	$is_adm=$auth->acl_get('a_') && !$is_guest ? 1 : 0;//0
	$is_mod=($auth->acl_get('m_') || $auth->acl_getf_global('m_')) && !$is_guest ? 1 : 0;//1
	$is_canaddchatm=$auth->acl_get('f_canaddchatm', $chat_id) && $auth->acl_get('u_canaddchatm') ? 1 : 0;//2
	$is_canviewchatu=$auth->acl_get('f_canviewchatu', $chat_id) && $auth->acl_get('u_canviewchatu') ? 1 : 0;//3
	$is_canviewchatm=$auth->acl_get('f_canviewchatm', $chat_id) && $auth->acl_get('u_canviewchatm') ? 1 : 0;//4
	$is_candelchatm=$auth->acl_get('f_candelchatm', $chat_id) && $auth->acl_get('u_candelchatm') ? 1 : 0;//5
	$is_canskipchatktime=$auth->acl_get('f_canskipchatktime', $chat_id) && $auth->acl_get('u_canskipchatktime') ? 1 : 0;//6
	$is_canhiddeninchat=$auth->acl_get('f_canhiddeninchat', $chat_id) && $auth->acl_get('u_canhiddeninchat') ? 1 : 0;//7
	$is_canviewhiddinchat=$auth->acl_get('f_canviewhiddinchat', $chat_id) && $auth->acl_get('u_canviewhiddinchat') ? 1 : 0;//8
	$is_cansendchatpm=$config['ppkbb_chat_enable_pm'] && $auth->acl_get('f_cansendchatpm', $chat_id) && $auth->acl_get('u_cansendchatpm') ? 1 : 0;//9
	$is_canskipdisbutton=$auth->acl_get('f_canskipdisbutton', $chat_id) && $auth->acl_get('u_canskipdisbutton') ? 1 : 0;//10
	$is_canusesmilies=$auth->acl_get('f_canusesmilies', $chat_id) && $auth->acl_get('u_canusesmilies') ? 1 : 0;//11
	$is_canusebbcodes=$config['ppkbb_chat_enable_bbcodes'] && $auth->acl_get('f_canusebbcodes', $chat_id) && $auth->acl_get('u_canusebbcodes') ? 1 : 0;//12
	$is_candelchatm2=$auth->acl_get('f_candelchatm2', $chat_id) && $auth->acl_get('u_candelchatm2') && !$is_guest ? 1 : 0;//13
	$is_canusegmode=0;
	$is_canuseacommands=$config['ppkbb_chat_enable_acommands'] && $auth->acl_get('f_canuseacommands', $chat_id) && $auth->acl_get('u_canuseacommands') && !$is_guest ? 1 : 0;//15
	$is_canviewarchive=$config['ppkbb_chat_marchive'][0] && $auth->acl_get('f_canviewarchive', $chat_id) && $auth->acl_get('u_canviewarchive') ? 1 : 0;//16
	$is_canusebbimg=$config['ppkbb_chat_enable_bbcodes'] && $auth->acl_get('f_canusebbimg', $chat_id) && $auth->acl_get('u_canusebbimg') ? 1 : 0;//17
	$is_caneditchatm=$auth->acl_get('f_caneditchatm', $chat_id) && $auth->acl_get('u_caneditchatm') ? 1 : 0;//18
	$is_caneditchatm2=$auth->acl_get('f_caneditchatm2', $chat_id) && $auth->acl_get('u_caneditchatm2') && !$is_guest ? 1 : 0;//19
	$is_canviewavatars=$user->optionget('viewavatars') ? 1 : 0;//20

	//$user_avatar=$config['ppkbb_chat_avatars'][0] && $config['allow_avatar'] && $user->data['user_avatar'] ? $user->data['user_avatar'] : '';
	$user_avatar=/*$is_canviewavatars && */$config['ppkbb_chat_avatars'][0] ? get_user_avatar($user->data['user_avatar'], $user->data['user_avatar_type'], $user->data['user_avatar_width'], $user->data['user_avatar_height']) : '';
	if($user_avatar)
	{
		preg_match('/src="(.*?)"/', $user_avatar, $avatar_src);
		if(isset($avatar_src[1]) && $avatar_src)
		{
			$user_avatar=preg_replace('#^\./#', '', $avatar_src[1]);
		}
	}

	$rights="$is_adm $is_mod $is_canaddchatm $is_canviewchatu $is_canviewchatm $is_candelchatm $is_canskipchatktime $is_canhiddeninchat $is_canviewhiddinchat $is_cansendchatpm $is_canskipdisbutton $is_canusesmilies $is_canusebbcodes $is_candelchatm2 $is_canusegmode $is_canuseacommands $is_canviewarchive $is_canusebbimg $is_caneditchatm $is_caneditchatm2 $is_canviewavatars";

	intval($config['ppkbb_chat_murefresh']) > 9 ? '' : $config['ppkbb_chat_murefresh']=10;

	!$is_canviewchatm ? $is_canaddchatm=0 : '';
	if($chat=='enter' && !$chat_user && ($is_canviewchatu || $is_canviewchatm))
	{
		/*if(!$chat_key)
		{

		}*/

		if($is_guest)
		{
			$config['ppkbb_chat_guests_option'][0] ? $user_lang=$config['ppkbb_chat_guests_option'][0] : $user_lang=$user->data['user_lang'];
			isset($user->lang[$config['ppkbb_chat_guests_option'][2]]) ? $username=$user->lang[$config['ppkbb_chat_guests_option'][2]] : $username=$config['ppkbb_chat_guests_option'][2] ? $config['ppkbb_chat_guests_option'][2] : $user->data['username'];
			$config['ppkbb_chat_guests_option'][1] ? $username=$username.'['.rand(1, $config['ppkbb_chat_guests_option'][1]).']' : '';
			$user_lang='ru';
			$user_dateformat = $config['default_dateformat'];
			$user_timezone = $config['board_timezone'];
			$user_dst = $config['board_dst'];
		}
		else
		{
			$chat_key=create_chatkey($user_id);
			if(!$chat_key)
			{
				trigger_error('CHAT_CHATKEY_ERROR');
			}
			$user_lang=$user->data['user_lang'];
			$user_dateformat = $user->data['user_dateformat'];
			$user_timezone = $user->data['user_timezone'];
			$user_dst = $user->data['user_dst'];

		}
		//$username = str_replace('-', '&minus;', $username);
		$query=('INSERT INTO ' .PPKCHAT_USERS_TABLE. " (username, rights, lastpost, room, lastaccess, user_id, chatkey, user_color, user_hidden, user_ip, user_lang, user_timezone, user_dst, user_dateformat, user_avatar, session_id) VALUES ('".$db->sql_escape($username)."', '{$rights}', '".($dt-$config['ppkbb_chat_waittime'])."', '{$chat_id}', '".($dt-$config['ppkbb_chat_murefresh'])."', '{$user_id}', '{$chat_key}', '{$user->data['user_colour']}', '".($is_canhiddeninchat ? 1 : 0)."', '".$db->sql_escape($user_ip)."', '{$user_lang}', '{$user_timezone}', '{$user_dst}', '".$db->sql_escape($user_dateformat)."', '".$db->sql_escape($user_avatar)."', '".$db->sql_escape($user->session_id)."')");
		$result=$db->sql_query($query);
		$chat_action='exit';
		$chat_user=1;
	}

	!$chat_action ? $chat_action='enter' : '';
	!$chat ? $chat='exit' : '';
	$link=$chat_link[$chat_action];

	if($is_canviewchatm && ($chat_user || $config['ppkbb_chat_enable']==3))
	{
		$message_window=true;
		$query='SELECT MAX(id) id, MAX(date) date FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE room='{$chat_id}'";
		$result=$db->sql_query($query);
		$max_id=$db->sql_fetchrow($result);
	}
	else
	{
		if(!$is_canviewchatm)
		{
			$message_window=$user->lang['CHAT_CANT_VIEWCHATM'];
		}
	}

	if($is_canviewchatu && $chat_user)
	{
		$user_window=true;
	}
	else
	{
		if(!$chat_user)
		{
			$online_window=true;
			$users_online=array();
			if(sizeof($users))
			{
				foreach($users as $k => $v)
				{
					$user_rights=explode(' ', $v['rights']);
					if($v['user_hidden'] && $user_rights[7] && !$is_canviewhiddinchat || $v['lastaccess'] > $dt)
					{

					}
					else
					{
						$users_online[$k]=$v['user_id'] > 1 ? '<a href="'.append_sid("{$phpbb_root_path}memberlist.{$phpEx}", "mode=viewprofile&amp;u=".$v['user_id']).'"'.($v['user_color'] ? ' style="color:#'.$v['user_color'].';"' : '').'><b>'.$v['username'].'</b></a>' : '<b>'.$v['username'].'</b>';
					}
				}
			}
			$online_message='<b>'.$user->lang['CHAT_IN'].': </b>'. (sizeof($users_online) ? implode(', ', $users_online).' ('.sizeof($users_online).')' : '(0)');
		}
	}

	if($is_canaddchatm && $is_canviewchatm && $chat_user)
	{
		$form_window=true;
		if($is_canusesmilies)
		{
			$bar_window=true;
		}
		if($is_canusebbcodes)
		{
			$bbcode_window=true;
		}
	}

	!$chat_user && $config['ppkbb_chat_enable']==3 ? $user->lang['CHAT_WAIT_MESSAGES']=sprintf($user->lang['CHAT_WAIT_MESSAGES'], $config['ppkbb_chat_murefresh']) : '';
}
if(!class_exists('timedelta'))
{
	$user->add_lang('mods/posts_merging');
	require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
}
$td = new timedelta();
$read_mode=!$chat_user && $config['ppkbb_chat_enable']==3 ? true : false;
$template->assign_vars(array(
	'S_MESSAGE_WINDOW'		=> $message_window ? true : false,
	'S_USER_WINDOW'		=> $user_window ? true : false,
	'S_FORM_WINDOW'		=> $form_window ? true : false,
	'S_BAR_WINDOW'		=> $bar_window ? true : false,

	'S_BBCODE_WINDOW'		=> $bbcode_window ? true : false,
	'S_ONLINE_WINDOW'		=> $online_window ? true : false,

	'S_BBCODE_ENABLED'		=> $config['ppkbb_chat_enable_bbcodes'] ? true : false,
	'S_BBCODE_IMG'		=> $is_canusebbimg ? true : false,
	'S_CHAT_NOTIN'			=> !$chat_user ? true : false,
	'S_READ_MODE'			=> $read_mode,

	'ONLINE_MESSAGE'		=> $online_window && $online_message ? $online_message : '',
	'U_CHAT_LINK'		=> $link,
	'FILENAME'	=> "{$phpbb_root_path}chat/message_user.{$phpEx}?f={$chat_id}&chatkey={$chat_key}".(!$chat_user && $config['ppkbb_chat_enable']==3 ? '&read_mode=1' : ''),
	'TIME'	=> isset($max_id['date']) ? intval($max_id['date']) : 0,
	'LAST_ID'	=> isset($max_id['id']) ? intval($max_id['id']) : 0,
	'REFRESH' => $config['ppkbb_chat_murefresh'] * 1000,
	'FORUM_ID'	=> $chat_id,
	'CHATKEY'	=> $chat_key,
	'WAITTIME'	=> $config['ppkbb_chat_waittime'],
	'FULLTIME'	=> $is_canskipdisbutton ? false : true,
	'CHATSMILIES'	=> $bar_window ? chat_generate_smilies() : '',
	'PHPEX'		=> $phpEx,
	'HIDDEN_OPT' => $is_canhiddeninchat ? true : false,
	'CHATPM_OPT' => $is_cansendchatpm ? true : false,
	'ACOMMANDS_OPT' => $is_canuseacommands ? true : false,
	'ADM_OPT' => $is_canuseacommands && $chat_user ? true : false,
	'ARCH_OPT' => $is_canviewarchive && $chat_user ? "{$phpbb_root_path}chat/message_user.{$phpEx}?f={$chat_id}&chatkey={$chat_key}&varch=1" : '',
	'SOUND_OPT'	=> $config['ppkbb_chat_sounds'][0] ? $config['ppkbb_chat_sounds'][0] : false,
	'SOUND_OPT_FILE'	=> $config['ppkbb_chat_sounds'][2],
	'SOUND_ON'	=> !$config['ppkbb_chat_sounds'][1] || ($config['ppkbb_chat_sounds'][0]==1 && $read_mode) ? false : true,

	'USER_ID' => $user_id,
	'QBAN_TIME' => $td->spelldelta(0, (!$config['ppkbb_chat_qbantime'] ? 3600 : $config['ppkbb_chat_qbantime'])),
	'ERROR' => $chat_error && isset($user->lang['CHAT_ERRDESCR'][$chat_error]) ? $user->lang['CHAT_ERRDESCR'][$chat_error] : false,

	'CHAT_TUPDATE' => sprintf($user->lang['CHAT_TUPDATE'], $config['ppkbb_chat_murefresh']),
	'CHAT_HSTEP' => $config['ppkbb_chat_height'][0],
	'CHAT_HMIN' => $config['ppkbb_chat_height'][1],
	'CHAT_HMAX' => $config['ppkbb_chat_height'][2],
	'CHAT_HEIGHT' => !$chat_user && $config['ppkbb_chat_enable']==3 ? $config['ppkbb_chat_height'][1] : $config['ppkbb_chat_height'][3],
	'CHAT_MINIMIZED' => $config['ppkbb_chat_enable']==2 ? true : false,
	'CHAT_WINDOW' => $chat_window ? true : false,
	'CHAT_EXIT' => $chat_exit,

	'IN_PORTAL' => defined('IN_PORTAL') ? true : false,
	'IN_CHAT' => defined('IN_CHAT') ? true : false,
	)
);

if(defined('IN_CHAT'))
{
	$template->assign_vars(array(
		'MODERATORS'	=> (!empty($moderators[$forum_id])) ? implode(', ', $moderators[$forum_id]) : '',

		'S_SINGLE_MODERATOR'	=> (!empty($moderators[$forum_id]) && sizeof($moderators[$forum_id]) > 1) ? false : true,
		'S_IS_LOCKED'			=> ($forum_data['forum_status'] == ITEM_LOCKED) ? true : false,
		'U_VIEW_FORUM'		=> append_sid("{$phpbb_root_path}{$gen_page}", "f={$forum_id}"),
		)
	);
}

//##############################################################################
//From includes/functions_posting.php
function chat_generate_smilies()
{
	global $db, $config, $phpbb_root_path;

	$display_link = false;
	$chat_smilies='';

	$sql = 'SELECT *
		FROM ' . SMILIES_TABLE .'
		ORDER BY smiley_order';
	$result = $db->sql_query($sql);

	$smilies = array();
	while($row=$db->sql_fetchrow($result))
	{
		if (empty($smilies[$row['smiley_url']]))
		{
			$smilies[$row['smiley_url']] = $row;
		}
	}
	$db->sql_freeresult($result);

	if (sizeof($smilies))
	{
		foreach ($smilies as $row)
		{
			$chat_smilies.='<a href="javascript:;" onClick="javascript:inserttext(\' '.$row['code'].' \');"><img style="margin:5px;" alt="'.$row['emotion'].'" title="'.$row['emotion'].'" width="'.$row['smiley_width'].'" height="'.$row['smiley_height'].'" src="'.$phpbb_root_path . $config['smilies_path'] . '/' . $row['smiley_url'].'" /></a><br />';
		}
	}

	return $chat_smilies;
}

function create_chatkey($user_id)
{
	global $user, $db;

	$str=$user->data['user_chatkey'];
	if(!$str)
	{
		$str=strtolower(gen_rand_string(8).gen_rand_string(8).gen_rand_string(8).gen_rand_string(8));

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_chatkey = '{$str}'
			WHERE user_id = '" . $user_id ."' LIMIT 1";
		$result=$db->sql_query($sql);
		if(!$result)
		{
			return false;
		}
	}

	return $str;
}
?>
