<?php

/**
*
* @package ppkBB3cker
* @version $Id: message_user.php 1.000 2008-10-04 11:10:00 PPK $
* @copyright (c) 2008 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

error_reporting(0);
@ini_set('register_globals', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);

function_exists('date_default_timezone_set') ? date_default_timezone_set('Europe/Moscow') : '';

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

require($phpbb_root_path.'config.'.$phpEx);

if(!in_array($dbms, array('mysql', 'mysqli')))
{
	err('Only mysql(i) supported');
}

$c=@mysql_connect($dbhost.($dbport ? ":{$dbport}" : ''), $dbuser, $dbpasswd);
if(!$c)
{
	err('Error connecting database: '.mysql_error().' ['.mysql_errno().']');
}

$s=@mysql_select_db($dbname, $c);
if(!$s)
{
	err('Error selecting database: '.mysql_error($c));
}

//my_sql_query("SET sql_mode='NO_UNSIGNED_SUBTRACTION'");
my_sql_query("SET NAMES 'utf8'");
unset($dbpasswd);

define('CONFIG_TABLE', $table_prefix . 'config');
define('SMILIES_TABLE', $table_prefix . 'smilies');
define('PPKCHAT_USERS_TABLE', $table_prefix . 'ppkchat_users');
define('PPKCHAT_MESSAGES_TABLE', $table_prefix . 'ppkchat_messages');

$config=array();

$tcachedir="{$phpbb_root_path}cache/";
$tincludedir="{$phpbb_root_path}tracker/tinc/";
$cincludedir="{$phpbb_root_path}chat/include/";

$cache_config=t_getcache('chat_config');
if($cache_config===false)
{
	include("{$cincludedir}cconf.{$phpEx}");
}
else
{
	foreach($cache_config as $k => $v)
	{
		$config[$k]=$v;
	}
	unset($cache_config);
}

$dt = time();
define('STRIP', (get_magic_quotes_gpc()) ? true : false);

$forum_id=my_request_var('f', '', 'my_int_val');
$varch=my_request_var('varch');
$chat_message=my_request_var('message', 'post', 'string', true);
$del_message=my_request_var('del_mess', 'post', 'my_int_val');
$edit_message=my_request_var('edit_mess', 'post', 'my_int_val');
$set_hidden=my_request_var('set_hidden', 'post');
$edit_mode=my_request_var('edit_mode', 'post', 'my_int_val');
$in_read_mode=my_request_var('read_mode', '', 'my_int_val');
$mode=my_request_var('mode', 'post');
$to_user=my_request_var('to_user', 'post');
$acomm=my_request_var('acomm', 'post');
$to_user_id=my_request_var('to_user_id', 'post', 'my_int_val');
$to_user ? '' : $to_user_id=0;
$chat_key=my_request_var('chatkey', '', 'string');
$lang='ru';

$sql='SELECT * FROM ' .PPKCHAT_USERS_TABLE. " WHERE room='{$forum_id}'";
$result=my_sql_query($sql);
$users=$chat_user=array();
while($row=mysql_fetch_array($result))
{
	if($row['room']==$forum_id)
	{
		if($row['chatkey']==$chat_key)
		{
			$chat_user=$row;
		}
		$users[$row['user_id']]=$row;
	}
}
mysql_free_result($result);

$message_table=$user_table=$acommand='';
$reason=$last_message_id=$last_user_id=$skip_lacheck=0;
intval($config['ppkbb_chat_murefresh']) > 9 ? '' : $config['ppkbb_chat_murefresh']=10;
$user_banned=$chat_user && $chat_user['lastaccess'] > $dt ? $chat_user['lastaccess'] : false;

if($chat_user || $config['ppkbb_chat_enable']==3)
{
	$read_mode=!$chat_user && $config['ppkbb_chat_enable']==3 || $user_banned ? true : false;

	if(!$in_read_mode)
{
	if($chat_user['lastaccess'] > $dt)
	{
		userkick(1);
	}
	else if($chat_user['lastaccess'] == 1)
	{
		//$sql="DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE room='{$forum_id}' AND user_id='{$chat_user['user_id']}' AND lastaccess = 1";
		userkick(5);
		}
	}

	if(!$read_mode)
	{
	$rights=$chat_user['rights'];
	$lang=$chat_user['user_lang'];
	$user_dateformat=$chat_user['user_dateformat'];
	$user_dst=$chat_user['user_dst'] * 3600;
	$user_timezone=$chat_user['user_timezone'] * 3600;
		$chat_user['user_id'] < 2 && $config['ppkbb_chat_guests_option'][0] ? $lang=$config['ppkbb_chat_guests_option'][0] : '';
	}
	else
	{
		$mode='read';
		$del_message=0;
		$chat_user=array();
		$lang=$config['default_lang'];
		$user_dateformat=$config['default_dateformat'];
		$user_dst=$config['board_dst'] * 3600;
		$user_timezone=$config['board_timezone'] * 3600;
	}
	!$lang ? $lang='ru' : '';
	$lang=basename($lang);
	if(!file_exists("{$phpbb_root_path}language/{$lang}/mods/ppkbb3cker_chat.{$phpEx}"))
	{
		err($lang['CHAT_LANG_ERROR']);
	}
	require_once("{$phpbb_root_path}language/{$lang}/mods/ppkbb3cker_chat.{$phpEx}");
	//0-$is_adm 1-$is_mod 2-$is_canaddchatm 3-$is_canviewchatu
	//4-$is_canviewchatm 5-$is_candelchatm 6-$is_canskipchatktime
	//7-$is_canhiddeninchat 8-$is_canviewhiddinchat 9-$is_cansendchatpm
	//10-$is_canskipdisbutton 11-$is_canusesmilies 12-$is_canusebbcodes
	//13-$is_candelchatm2 14-$is_canusegmode 15-$is_canuseacommands
	//16-$is_canviewarchive 17-$is_canusebbimg 18-$is_caneditchatm 19-$is_caneditchatm2
	//20-$is_canviewavatars
	$user_color=$user_rights=array();
	if(!$read_mode)
	{
		$rights=my_split_config($rights, 21, 'my_int_val');

		$config['ppkbb_chat_enable_pm'] ? '' : $rights[9]=0;
		$config['ppkbb_chat_enable_acommands'] ? '' : $rights[15]=0;
		$config['ppkbb_chat_marchive'][0] && $rights[16] ? '' : $varch=0;
	}
	else
	{
		$rights=array_fill(0, 20, 0);
		$rights[4]=1;
		$varch=0;
	}

	if(!$user_banned)
	{
		if($rights[15] && $chat_user['user_id'] > 1)
		{
			if($acommand=substr($acomm, 0, 2))
			{
				if(in_array($acommand, array('/m', '/r', '/u', '/k', '/b', '/q', '/a')))
				{
					include($phpbb_root_path.'chat/include/cacommchat.'.$phpEx);
				}
			}
	}

	if($del_message)
	{
		if($rights[4] && $rights[5])//is_canviewchatm is_candelchatm
		{

					$sql='SELECT room, user_id FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE id='{$del_message}' AND (to_user='0' OR to_user='{$chat_user['user_id']}')"
						.(!$rights[13] ? " AND user_id='{$chat_user['user_id']}'" : '')
						.(!$config['ppkbb_chat_bot'][2] ? " AND user_id!='0'" : '')
							.' LIMIT 1';
					$result=my_sql_query($sql);
					$del_data=mysql_fetch_array($result);
					mysql_free_result($result);

				if(isset($del_data['user_id']))
				{
					$sql='DELETE FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE id='{$del_message}'"
						.(!$rights[13] ? " AND user_id='{$chat_user['user_id']}' " : '')
						.(!$config['ppkbb_chat_bot'][2] ? " AND user_id!='0'" : '')
							.' LIMIT 1';
					$result=my_sql_query($sql);
					if($config['ppkbb_chat_logs'][3])
					{
						if($del_data)
						{
							$del_data['user_id'] > 1 ? '' : $del_data['user_id']=1;
							include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
							chat_add_log(3, $chat_user['user_id'], my_int_val($del_data['room']), mysql_real_escape_string($del_data['user_id'], $c), 'LOG_CHAT_DELMESS', '');
}
		}
	}
			}
		}
		else if($edit_message)
		{
			if($rights[18] || $rights[19])
			{
				$sql='SELECT message FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE id='{$edit_message}' AND (to_user='0' OR to_user='{$chat_user['user_id']}')"
					.(!$rights[19] ? " AND user_id='{$chat_user['user_id']}'" : '')
					.(!$config['ppkbb_chat_bot'][2] ? " AND user_id!='0'" : '')
						.' LIMIT 1';
				$result=my_sql_query($sql);
				$edit_data=mysql_fetch_array($result);
				mysql_free_result($result);
				if($edit_data)
				{
					err(htmlspecialchars_decode($edit_data['message']));
				}
			}
			err($lang['MESSAGE_EDIT_ERROR']);
		}
	else
	{
			if($set_hidden && $rights[3] && $rights[7])//is_canviewchatu is_canhiddeninchat
			{
				$sql='UPDATE ' .PPKCHAT_USERS_TABLE. " SET user_hidden='".($set_hidden=='true' ? 1 : 0)."' WHERE user_id={$chat_user['user_id']} AND room='{$forum_id}' LIMIT 1";
				$result=my_sql_query($sql);
				$users[$chat_user['user_id']]['user_hidden']=$set_hidden=='true' ? 1 : 0;
			}

		$lastpost=0;
		if(!$read_mode)
		{
				$chat_user['user_id']==$to_user_id ? $to_user_id=0 : '';

		if($chat_message!=='' && $mode=='add')
		{
			include($phpbb_root_path.'chat/include/caddchatm.'.$phpEx);
		}

				$sql='UPDATE ' .PPKCHAT_USERS_TABLE. ' SET lastaccess='
					.($lastpost ? "lastaccess-{$config['ppkbb_chat_murefresh']}" : "'{$dt}'")
					.($lastpost ? ", lastpost='{$lastpost}'" : '')
						." WHERE chatkey='".mysql_real_escape_string($chat_key, $c)."' AND room='{$forum_id}'";
				$result=my_sql_query($sql);
		}
		if($rights[3])//is_canviewchatu
		{
			/*if($config['ppkbb_chat_autokill_onrefresh'] && !$skip_lacheck && ($dt - $chat_user['lastaccess']) < $config['ppkbb_chat_murefresh'] - 1)
			{
				userkick(2);
			}
			else
			{*/
				$config['script_path']=$config['script_path']!='/' ? $config['script_path'].'/' : '/';
				foreach($users as $v)
				{
					$user_rights[$v['user_id']]=explode(' ', $v['rights']);

						if($v['user_hidden'] && $user_rights[$v['user_id']][7] && !$rights[8])//is_canhiddeninchat is_canviewhiddinchat
						{

						}
						else
						{
						$v['username']=stripslashes($v['username']);
							$v['username'] = str_replace('-', '&minus;', $v['username']);
							$this_user_banned=$v['lastaccess'] > $dt ? 1 : 0;
						$user_table.= '
							<div id="u'.$v['user_id'].'">'
								.(1/*!$this_user_banned && $v['user_id']!=$chat_user['user_id']*/ ? '<a title="'.$lang['CHAT_TO_USER'].'" onClick="javascript:inserttext(\''.addslashes($v['username']).': \');" href="javascript:;">&rarr;</a>' : '')
								.'&nbsp;<a'
								.($this_user_banned ? ' title="'.($rights[0] ? 'IP: '.long2ip($v['user_ip']).', ' : '').sprintf($lang['CHAT_UBANNED'], date('Y-m-d H:i:s', $v['lastaccess'])).'"' : ($rights[0] ? ' title="IP: '.long2ip($v['user_ip']).'"' : '')).' style="font-weight:bold;'
								.($v['user_color'] ? 'color:#'.$v['user_color'].';' : '')
								.($this_user_banned ? 'text-decoration:line-through;' : '').';" href="'
								.($v['user_id'] > 1 ? $config['script_path'].'memberlist.'.$phpEx.'?mode=viewprofile&amp;u='.$v['user_id'].($chat_user['session_id'] ? "&amp;sid={$chat_user['session_id']}" : '').'" target="_blank"' : 'javascript:;"').'>'
								.($user_rights[$v['user_id']][7] && $rights[8] ? '<i>'.$v['username'].'</i>' : $v['username']).'</a>'
								.($v['user_hidden'] && $rights[8] ? '&nbsp;<img src="./chat/images/overview.png" alt="" />' : '')
								.(/*!$v['user_hidden'] && */!$this_user_banned && $rights[9] && $user_rights[$v['user_id']][9] && $v['user_id']!=$chat_user['user_id'] ? '&nbsp;<a href="javascript:;" onClick="javascript:insertuname(\''.addslashes($v['username']).'\', '.$v['user_id'].');" title="'.$lang['CHAT_PMHLP'].'"><img src="./chat/images/identity.png" alt="'.$lang['CHAT_PM'].'" /></a>' : '')

								.(!$user_rights[$v['user_id']][15] && $rights[15] && $v['user_id']!=$chat_user['user_id'] ? ' <a title="'.$lang['CHAT_USER_ADM'].'" href="javascript:;" onClick="javascript:aopts(\''.$v['user_id'].'\', \''.$chat_user['user_id'].'\', \''
								.($this_user_banned ? 'unban' : 'ban').'\');"><img src="./chat/images/redled.png" alt="" /></a> ' : '').'<b id="aopts'.$v['user_id'].'"></b>
						</div>';
						}
				}
			//}
		}
		else
		{
			$user_table= '<div><span class="textuser">'.$lang['CHAT_CANT_VIEWCHATU'].'</span></div>';
		}
		if($rights[4])//is_canviewchatm
		{
			/*if($config['ppkbb_chat_autokill_onrefresh'] && !$skip_lacheck && ($dt - $chat_user['lastaccess']) < $config['ppkbb_chat_murefresh'] - 1)
			{
				userkick(3);
			}
			else
			{*/
				$smiles=get_chat_smilies();
				intval($config['ppkbb_chat_messdisplay']) ? '' : $config['ppkbb_chat_messdisplay']=25;
					$sql='SELECT * FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE "
						.($rights[9] ? "(to_user='0' OR to_user='{$chat_user['user_id']}' OR (to_user!='0' AND user_id='{$chat_user['user_id']}'))" : "to_user='0'")
						." AND (room='{$forum_id}' OR room='0')"
						.($config['ppkbb_chat_marchive'][0] && $varch ? ' AND date > '.($dt - ($config['ppkbb_chat_marchive'][0])) : '')
							." ORDER BY id DESC "
								.($varch ? "LIMIT {$config['ppkbb_chat_messdisplay']}, ".($config['ppkbb_chat_messdisplay']+$config['ppkbb_chat_marchive'][1]) : "LIMIT {$config['ppkbb_chat_messdisplay']}");
					$result=my_sql_query($sql);

				if($config['ppkbb_chat_enable_magicurl'])
				{
					//From /includes/constants.php
					// Magic url types
					define('MAGIC_URL_EMAIL', 1);
					define('MAGIC_URL_FULL', 2);
					define('MAGIC_URL_WWW', 4);
					include($phpbb_root_path.'chat/include/cmgurl.'.$phpEx);
				}

				$last_mess_id=$i=0;
					while($row=mysql_fetch_array($result))
				{
						if(!$varch && $config['ppkbb_chat_marchive'][0] && $dt-$row['date'] > $config['ppkbb_chat_marchive'][0])
					{
						continue;
					}

					/*if($varch && $i<$config['ppkbb_chat_messdisplay'])
					{
						$i+=1;
						continue;
					}*/
						if(!$read_mode && !isset($user_rights[$row['user_id']]))
					{
							$user_rights[$row['user_id']]=my_split_config($v['rights'], 21, 'my_int_val');
					}

						//$row['message']=htmlspecialchars(stripslashes($row['message']));
						//$row['message']=stripslashes($row['message']);
					//$row['message']=wordwrap($row['message'], 32, '<wbr>', 1);
						$row['message'] = str_replace('-', '&minus;', $row['message']);

						if(!$read_mode && $user_rights[$row['user_id']][11] && $rights[11] && !$varch)
					{
						$row['message']=chat_smilies($row['message'], $smiles['match'], $smiles['replace'], $config['ppkbb_chat_maxsmiles']);
					}
						if($config['ppkbb_chat_enable_magicurl'] && !$varch)
					{
						$row['message']=make_clickable($row['message']);
					}
						$mess_addon=$user_avatar='';
						$is_bot=!$row['user_id'] ? true : false;
						$is_guest=$row['user_id'] < 0 ? true : false;

						if($config['ppkbb_chat_avatars'][0] && $config['allow_avatar'] && !$read_mode && $rights[20])
						{
							$avatar_height=@$users[$row['user_id']]['user_avatar'] ? 0 : $config['ppkbb_chat_avatars'][0];
							$user_avatar=!$avatar_height ? $users[$row['user_id']]['user_avatar'] : $config['script_path'].'chat/images/'.($is_bot ? $config['ppkbb_chat_bot'][5] : $config['ppkbb_chat_avatars'][1]);
							$user_avatar='<img src="' .$user_avatar. '" width="' . $config['ppkbb_chat_avatars'][0] . '"'.($avatar_height ? ' height="'.$avatar_height.'" style="border:1px solid #CFCFCF;"' : '').' alt="" />';
						}

					$varch ? $mess_addon.=($row['user_id'] > 1 ? ' <a target="_blank" href="'.$config['script_path'].'memberlist.'.$phpEx.'?mode=viewprofile&amp;u='.$row['user_id'].($chat_user['session_id'] ? "&amp;sid={$chat_user['session_id']}" : '').'">&uarr;</a> ' : '') : '';

						$is_pm=false;
						if($rights[9] && $chat_user['user_id']==$row['to_user'])
						{
							$is_pm=true;
							$mess_addon.=$lang['CHAT_PMIN'].(@$users[$row['user_id']] ? '<b style="color:#'.$users[$row['user_id']]['user_color'].'">'.$users[$row['user_id']]['username'].'</b>' : $row['username'].($varch ? " {$lang['CHAT_PMFOR']} " : ''));
						}
						else if($rights[9] && $row['to_user'] && $row['user_id']==$chat_user['user_id'])
						{
							$is_pm=true;
							$mess_addon.=$lang['CHAT_PMOUT'].(@$users[$row['to_user']] ? '<b style="color:#'.$users[$row['to_user']]['user_color'].'">'.$users[$row['to_user']]['username'].'</b>' : '<a target="_blank" href="'.$config['script_path'].'memberlist.'.$phpEx.'?mode=viewprofile&amp;u='.$row['to_user'].($chat_user['session_id'] ? "&amp;sid={$chat_user['session_id']}" : '').'"> #'.$row['to_user'].'</a>').($varch ? " {$lang['CHAT_PMFROM']} " : '');
						}
						else
						{
							$is_bot && isset($lang[$config['ppkbb_chat_bot'][1]]) ? $row['username']=$lang[$config['ppkbb_chat_bot'][1]] : '';
							$config['ppkbb_chat_bot'][3] && $is_bot ? $row['user_color']=$config['ppkbb_chat_bot'][3] : '';
							$config['ppkbb_chat_bot'][4] && $is_bot ? $row['message']='<font style="color:#'.$config['ppkbb_chat_bot'][4].';">'.$row['message'].'</font>' : '';
						$mess_addon.=' <b'.($varch ? ' title="'.($rights[0] ? 'IP: '.long2ip($row['user_ip']) : '').'"' : '').' style="'.($row['user_color'] ? 'color:#'.$row['user_color'] : '').';">'.$row['username'].'</b> ';
						}

					$message_table.= '
						<div style="width:100%;" id="p'.$row['id'].'" class="m'.($is_pm ? ' pm' : '').'">'.
							(!$varch && $user_avatar && $rights[20] ? '<table width="100%"><tr><td width="'.$config['ppkbb_chat_avatars'][0].'px">'.$user_avatar.'</td><td valign="top">&nbsp;' : '').
							format_date($row['date']).$mess_addon.' '.
							((($rights[5] && $row['user_id']==$chat_user['user_id']) || ($rights[13] && $row['user_id']) || ($rights[13] && $is_bot && $config['ppkbb_chat_bot'][2])) && !$varch && !$row['to_user'] ? ' <a title="'.$lang['MESS_DEL'].'" href="javascript:;" onclick="delete_post(\''.$row['id'].'\')">['.
						($row['user_id']!=$chat_user['user_id'] ? 'x' : '<b>x</b>').']</a> ' : '').
							((($rights[18] && $row['user_id']==$chat_user['user_id']) || ($rights[19] && $row['user_id']) || ($rights[19] && $is_bot && $config['ppkbb_chat_bot'][2])) && !$varch && !$row['to_user'] ? ' <a title="'.$lang['MESS_EDIT'].'" href="javascript:;" onclick="edit_post(\''.$row['id'].'\')">['.
							($row['user_id']!=$chat_user['user_id'] ? 'e' : '<b>e</b>').']</a> ' : '').
							(!$varch && $user_avatar && $rights[20] ? '<br />&nbsp;' : '').
							'<font class="c_mess" id="e'.$row['id'].'">'.$row['message'].'</font>'.
							(!$varch && $user_avatar && $rights[20] ? '</td></tr></table>' : '').
						'</div>';

						if(!$last_message_id)
						{
							$last_message_id=$row['id'];
							$last_user_id=$row['user_id'];
						}
				}
					mysql_free_result($result);

					if($config['ppkbb_chat_cleanup_interval'] && $dt - $config['ppkbb_chat_last_cleanup'] > $config['ppkbb_chat_cleanup_interval'])
				{
					if($config['ppkbb_chat_marchive'][0])
					{
							$sql="SELECT id FROM ".PPKCHAT_MESSAGES_TABLE." WHERE date < ".($dt - ($config['ppkbb_chat_marchive'][0])) ." ORDER BY id DESC LIMIT 1";
					}
					else
					{
						$sql="SELECT id FROM ".PPKCHAT_MESSAGES_TABLE.' ORDER BY id DESC LIMIT '.($config['ppkbb_chat_messdisplay']-1).', 1';
					}
						$result=my_sql_query($sql);
						$last_mess_id=mysql_fetch_row($result);
						$last_mess_id=my_int_val($last_mess_id[0]);
						mysql_free_result($result);

					if($last_mess_id)
					{
						$sql='DELETE FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE id <= '{$last_mess_id}'";
							$result=my_sql_query($sql);
					}

					$sql='UPDATE ' .CONFIG_TABLE. " SET config_value='{$dt}', is_dynamic='1' WHERE config_name='ppkbb_chat_last_cleanup'";
						$result=my_sql_query($sql);

						include_once("{$tincludedir}tcache.{$phpEx}");
						t_cleancache('chat_config');
				}
			//}
		}
		else
		{
			$message_table= '<div><span class="textmess">'.$lang['CHAT_CANT_VIEWCHATM'].'</span></div>';
		}
	}
	if(!$read_mode)
	{
	if(!$rights[6] && $dt - @$chat_user['lastpost'] > $config['ppkbb_chat_killtime'])
	{
				$result=my_sql_query("DELETE FROM ".PPKCHAT_USERS_TABLE." WHERE room='{$forum_id}' AND user_id='{$chat_user['user_id']}'");
		userkick(4);
	}
}
}
}
else
{
	include($phpbb_root_path.'chat/include/cnochat.'.$phpEx);
}

if($varch)
{
	if($rights[16])
	{
		include($phpbb_root_path.'chat/include/cvarchchat.'.$phpEx);

		header('Content-type: text/html; charset=UTF-8');

		view_archive($message_table);
	}
}
else
{
	header('Content-type: text/html; charset=UTF-8');

	if($user_banned)
	{
		$message_table=sprintf($lang['CHAT_BANNED'], date('Y-m-d H:i:s', $user_banned));
	}
	echo "{$message_table}---{$last_message_id}---{$user_table}---{$dt}---".($config['ppkbb_chat_murefresh']*1000)."---{$reason}---{$last_user_id}";
}

if($c)
{
	mysql_close($c);
}

exit();

//############################################################

function get_chat_smilies()
{
	global $config, $phpbb_root_path, $tincludedir, $phpEx;
	static $match;
	static $replace;

	// See if the static arrays have already been filled on an earlier invocation
	if (!is_array($match))
	{
		$match = $replace = array();
		$smiles=array();

		$cache_smiles=t_getcache('chat_smiles');
		if($cache_smiles===false)
		{
			$sql = 'SELECT code, smiley_url, emotion
			FROM ' . SMILIES_TABLE . '
			ORDER BY LENGTH(code) DESC';

			$result = my_sql_query($sql);

			while($row=mysql_fetch_array($result))
		{
			if (empty($row['code']))
			{
				continue;
			}

				$row['code']=htmlspecialchars_decode($row['code']);
				$cache_smiles[]=array('code'=>base64_encode($row['code']), 'smiley_url'=>$row['smiley_url'], 'emotion'=>base64_encode($row['emotion']));
			$smiles['match'][] = '(?<=^|[\n .])' . preg_quote($row['code'], '#') . '(?![^<>]*>)';
			$smiles['replace'][] = '<img src="./'.$config['smilies_path'].'/' . $row['smiley_url'] . '" alt="' . $row['code'] . '" title="' . $row['emotion'] . '" />';
		}
			mysql_free_result($result);

			include_once("{$tincludedir}tcache.{$phpEx}");
			t_recache('chat_smiles', $cache_smiles);
		}
		else
		{
			foreach($cache_smiles as $k => $v)
			{
				if (empty($v['code']))
				{
					continue;
				}
				$v['code']=base64_decode($v['code']);
				$v['emotion']=base64_decode($v['emotion']);
				$smiles['match'][] = '(?<=^|[\n .])' . preg_quote($v['code'], '#') . '(?![^<>]*>)';
				$smiles['replace'][] = '<img src="./'.$config['smilies_path'].'/' . $v['smiley_url'] . '" alt="' . $v['code'] . '" title="' . $v['emotion'] . '" />';
			}
			unset($cache_smiles);
		}
	}

	return $smiles;
}

function chat_smilies($text, $match, $replace, $max_smilies = 0)
{
	if (sizeof($match))
	{
		if ($max_smilies)
		{
			$num_matches = preg_match_all('#' . implode('|', $match) . '#', $text, $matches);
			unset($matches);

			if ($num_matches !== false && $num_matches > $max_smilies)
			{
				return $text;
			}
		}

		// Make sure the delimiter # is added in front and at the end of every element within $match
		$text = trim(preg_replace(explode(chr(0), '#' . implode('#' . chr(0) . '#', $match) . '#'), $replace, $text));
	}

	return $text;
}

//From includes/utf/utf_tools.php
function utf8_strlen($text)
{
	// Since utf8_decode is replacing multibyte characters to ? strlen works fine
	return strlen(utf8_decode($text));
}

function err($str)
{
	global $c;

	header('Content-type: text/html; charset=UTF-8');
	echo $str;

	if($c)
	{
		mysql_close($c);
	}

	exit();
}

function userkick($n=0)
{
	global $last_message_id, $dt;

	$message_table='&nbsp';
	$user_table='&nbsp;';

	err("{$message_table}---{$last_message_id}---{$user_table}---{$dt}---3600000---{$n}");
}

function my_int_val($v=0, $max=0, $drop=false, $negative=false)
{
	if(!$v || ($v < 0 && !$negative))
	{
		return 0;
	}
	else if($drop && $v>$max)
	{
		return 0;
	}
	else if($max && $v>$max)
	{
		return $max;
	}

	return @number_format($v+0, 0, '', '');
}

function my_float_val($v=0, $n=3, $max=0, $drop=false, $negative=false)
{
	if(!$v || ($v < 0 && !$negative))
	{
		return "0.".str_repeat('0', $n);
	}
	else if($drop && $v>$max)
	{
		return "0.".str_repeat('0', $n);
	}
	else if($max && $v>$max)
	{
		return $max;
	}

	return @number_format($v+0, $n, '.', '');
}

function t_getcache($t, $var='')
{
	global $tcachedir, $phpEx;

	$cache_data=array();
	$f_name="{$tcachedir}data_ppkbb3cker_{$t}.{$phpEx}";
	if(@file_exists($f_name))
	{
		include($f_name);

		return $var ? $$var : $cache_data;
	}

	return false;
}

function my_split_config($config, $count=0, $type=false, $split='')
{
	$count=intval($count);

	if(!$count && $config==='')
	{
		return array();
	}

	$s_config=$count > 0 ? @explode($split ? $split : ' ', $config, $count) : @explode($split ? $split : ' ', $config);
	$count=$count > 0 ? $count : sizeof($s_config);
	if($count)
	{
		for($i=0;$i<$count;$i++)
		{
			if($type)
			{
				if(is_array($type) && @function_exists(@$type[$i]))
				{
					$s_config[$i]=call_user_func($type[$i], @$s_config[$i]);
				}
				else if(@function_exists($type))
				{
					$s_config[$i]=call_user_func($type, @$s_config[$i]);
				}
				else
				{
					$s_config[$i]=@$s_config[$i];
				}
			}
			else
			{
				$s_config[$i]=@$s_config[$i];
			}
		}
	}

	return $s_config;
}

//From /includes/sessions.php
function format_date($gmepoch, $format = false, $forcedate = false)
{
	static $midnight;
	static $date_cache;

	global $user_timezone, $user_dst, $user_dateformat, $lang;

	$date_format = $user_dateformat;
	$timezone = $user_timezone;
	$dst = $user_dst;

	$format = (!$format) ? $date_format : $format;
	$now = time();
	$delta = $now - $gmepoch;

	if (!isset($date_cache[$format]))
	{
		// Is the user requesting a friendly date format (i.e. 'Today 12:42')?
		$date_cache[$format] = array(
			'is_short'		=> strpos($format, '|'),
			'format_short'	=> substr($format, 0, strpos($format, '|')) . '||' . substr(strrchr($format, '|'), 1),
			'format_long'	=> str_replace('|', '', $format),
			'lang'			=> $lang['datetime'],
		);

		// Short representation of month in format? Some languages use different terms for the long and short format of May
		if ((strpos($format, '\M') === false && strpos($format, 'M') !== false) || (strpos($format, '\r') === false && strpos($format, 'r') !== false))
		{
			$date_cache[$format]['lang']['May'] = $lang['datetime']['May_short'];
		}
	}

	// Zone offset
	$zone_offset = $timezone + $dst;

	// Show date <= 1 hour ago as 'xx min ago' but not greater than 60 seconds in the future
	// A small tolerence is given for times in the future but in the same minute are displayed as '< than a minute ago'
	/*if ($delta <= 3600 && $delta > -60 && ($delta >= -5 || (($now / 60) % 60) == (($gmepoch / 60) % 60)) && $date_cache[$format]['is_short'] !== false && !$forcedate && isset($lang['datetime']['AGO']))
	{
		return lang(array('datetime', 'AGO'), max(0, (int) floor($delta / 60)));
	}*/

	if (!$midnight)
	{
		list($d, $m, $y) = explode(' ', gmdate('j n Y', time() + $zone_offset));
		$midnight = gmmktime(0, 0, 0, $m, $d, $y) - $zone_offset;
	}

	if ($date_cache[$format]['is_short'] !== false && !$forcedate && !($gmepoch < $midnight - 86400 || $gmepoch > $midnight + 172800))
	{
		$day = false;

		if ($gmepoch > $midnight + 86400)
		{
			$day = 'TOMORROW';
		}
		else if ($gmepoch > $midnight)
		{
			$day = 'TODAY';
		}
		else if ($gmepoch > $midnight - 86400)
		{
			$day = 'YESTERDAY';
		}

		if ($day !== false)
		{
			return str_replace('||', $lang['datetime'][$day], @strtr(@gmdate($date_cache[$format]['format_short'], $gmepoch + $zone_offset), $date_cache[$format]['lang']));
		}
	}

	return @strtr(@gmdate($date_cache[$format]['format_long'], $gmepoch + $zone_offset), $date_cache[$format]['lang']);
}

function lang()
{
	global $lang;

	$args = func_get_args();
	$key = $args[0];

	if (is_array($key))
	{
		$lang = &$lang[array_shift($key)];

		foreach ($key as $_key)
		{
			$lang = &$lang[$_key];
		}
	}
	else
	{
		$lang = &$lang[$key];
	}

	// Return if language string does not exist
	if (!isset($lang) || (!is_string($lang) && !is_array($lang)))
	{
		return $key;
	}

	// If the language entry is a string, we simply mimic sprintf() behaviour
	if (is_string($lang))
	{
		if (sizeof($args) == 1)
		{
			return $lang;
		}

		// Replace key with language entry and simply pass along...
		$args[0] = $lang;
		return call_user_func_array('sprintf', $args);
	}

	// It is an array... now handle different nullar/singular/plural forms
	$key_found = false;

	// We now get the first number passed and will select the key based upon this number
	for ($i = 1, $num_args = sizeof($args); $i < $num_args; $i++)
	{
		if (is_int($args[$i]))
		{
			$numbers = array_keys($lang);

			foreach ($numbers as $num)
			{
				if ($num > $args[$i])
				{
					break;
				}

				$key_found = $num;
			}
			break;
		}
	}

	// Ok, let's check if the key was found, else use the last entry (because it is mostly the plural form)
	if ($key_found === false)
	{
		$numbers = array_keys($lang);
		$key_found = end($numbers);
	}

	// Use the language string we determined and pass it to sprintf()
	$args[0] = $lang[$key_found];
	return call_user_func_array('sprintf', $args);
}

function my_sql_query($query)
{
	global $c;

	$result=@mysql_query($query, $c);

	if(!$result)
	{
		err('Unknown sql error');
		mysql_close($c);
	}

	return $result;
}

function my_request_var($var_name, $array='get', $type='', $multibyte=false)
{
	$result='';

	switch($array)
	{
		case 'post':
			if(isset($_POST[$var_name]))
			{
				$result=$_POST[$var_name];
			}
		break;

		case 'cookie':
			if(isset($_COOKIE[$var_name]))
			{
				$result=$_COOKIE[$var_name];
			}
		break;

		case 'request':
			if(isset($_REQUEST[$var_name]))
			{
				$result=$_REQUEST[$var_name];
			}
		break;

		case 'get':
		default:
			if(isset($_GET[$var_name]))
			{
				$result=$_GET[$var_name];
			}
		break;
	}

	if($type=='array')
	{
		if(!is_array($result))
		{
			$result=array();
		}
	}
	else
	{
		if(is_array($result))
		{
			$result='';
		}
	}

	if(!is_array($result))
	{
		if ($type=='string')
		{
			$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $result), ENT_COMPAT, 'UTF-8'));

			if (!empty($result))
			{
				// Make sure multibyte characters are wellformed
				if ($multibyte)
				{
					if (!preg_match('/^./u', $result))
					{
						$result = '';
					}
				}
				else
				{
					// no multibyte, allow only ASCII (0-127)
					$result = preg_replace('/[\x80-\xFF]/', '?', $result);
				}
			}

			$result = (STRIP) ? stripslashes($result) : $result;
		}
		else if($type && @function_exists($type))
		{
			$result=call_user_func($type, $result);
		}
	}

	return $result;
}
?>
