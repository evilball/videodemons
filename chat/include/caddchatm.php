<?php
/**
*
* @package ppkBB3cker
* @version $Id: caddchatm.php 1.000 2010-03-14 13:14:00 PPK $
* @copyright (c) 2010 PPK
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

if($edit_mode)
{
	!$rights[18] && !$rights[19] ? $edit_mode=0 : '';
}
if($rights[4] && $rights[2])//is_canviewchatm is_canaddchatm
{
	if($edit_mode)
	{
		$to_user_id=$to_room=0;
	}

	if($config['ppkbb_chat_autokill_onpost'] && !$rights[10] && ($dt - $chat_user['lastpost']) < $config['ppkbb_chat_waittime'] - 1 && !$edit_mode)
	{
		userkick(2);
	}
	else
	{
		if(!$acommand)
	{
			//$chat_message = str_replace('-', '&minus;', $chat_message);
			//$chat_user['username'] = str_replace('-', '&minus;', $chat_user['username']);
			intval($config['ppkbb_chat_messlength']) ? '' : $config['ppkbb_chat_messlength']=255;
			$mbstring=extension_loaded('mbstring') ? 1 : 0;
			$chat_message_len=$mbstring ? mbstring_utf8_strlen($chat_message) : utf8_strlen($chat_message);

			if($chat_message_len > $config['ppkbb_chat_messlength'])
			{
				$chat_message=$mbstring ? mbstring_utf8_substr($chat_message, 0, $config['ppkbb_chat_messlength']) : utf8_substr($chat_message, 0, $config['ppkbb_chat_messlength']);
			}
			if($to_user_id && ((@!$users[$to_user_id] && !$rights[14])/* || @$users[$to_user_id]['user_hidden']*/ || @$users[$to_user_id]['lastaccess'] > $dt))
			{
				$to_user_id=0;
			}
			else
			{
				!$config['ppkbb_chat_enable_bbcodes'] || !$rights[12] ? $chat_message=preg_replace('#\[/?(b|i|u|pre|samp|code|colou?r(=[^\[]+)?|size(=[^\[]+)?|noparse|url(=[^\[]+)?|s|q|blockquote)\]#', '', $chat_message) : '';
				!$config['ppkbb_chat_enable_bbcodes'] || !$rights[17] ? $chat_message=preg_replace('#\[/?img(=[^\[]+)?\]#', '', $chat_message) : '';
				$chat_message=preg_replace('/sid=([a-zA-Z0-9]{32})(&amp;)?/', '', $chat_message);
				if($chat_message!=='')
				{
					if(!$edit_mode)
					{
						$sql='INSERT INTO ' .PPKCHAT_MESSAGES_TABLE. " (message, user_id, to_user, date, rights, room, username, user_color, user_ip) VALUES ('".mysql_real_escape_string($chat_message, $c)."', '{$chat_user['user_id']}', '".($rights[9] && $to_user_id ? $to_user_id : 0)."', '{$dt}', '{$chat_user['rights']}', '{$forum_id}', '".mysql_real_escape_string($chat_user['username'], $c)."', '{$chat_user['user_color']}', INET_ATON('".mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $c)."'))";
						$result=my_sql_query($sql);
					$lastpost=$dt;
					}
					else
					{
						$sql='UPDATE ' .PPKCHAT_MESSAGES_TABLE. " SET message='".mysql_real_escape_string($chat_message, $c)."', edited_by='{$chat_user['user_id']}' WHERE id='{$edit_mode}' AND (to_user='0' OR to_user='{$chat_user['user_id']}')".(!$rights[19] ? " AND user_id='{$chat_user['user_id']}'" : '').(!$config['ppkbb_chat_bot'][2] ? " AND user_id!='0'" : '');
						$result=my_sql_query($sql);
						if(mysql_affected_rows($c) && $config['ppkbb_chat_logs'][4])
						{
							$sql='SELECT room, user_id FROM ' .PPKCHAT_MESSAGES_TABLE. " WHERE id='{$edit_mode}' AND (to_user='0' OR to_user='{$chat_user['user_id']}')".(!$rights[19] ? " AND user_id='{$chat_user['user_id']}'" : '').(!$config['ppkbb_chat_bot'][2] ? " AND user_id!='0'" : '');
							$result=my_sql_query($sql);
							$edit_data=mysql_fetch_array($result);
							mysql_free_result($result);
							if($edit_data)
							{
								$edit_data['user_id'] > 1 ? '' : $edit_data['user_id']=1;
								include_once($phpbb_root_path.'chat/include/caddlogchat.'.$phpEx);
								chat_add_log(3, $chat_user['user_id'], my_int_val($edit_data['room']), mysql_real_escape_string($edit_data['user_id'], $c), 'LOG_CHAT_EDITMESS', '');
							}
						}
					}
					$skip_lacheck=1;
				}
	}
}
	}
}
else
{
	userkick(3);
}

//############################################################

//From includes/utf/utf_tools.php
function mbstring_utf8_substr($str, $offset, $length = null)
{
	if (is_null($length))
	{
		return mb_substr($str, $offset);
	}
	else
	{
		return mb_substr($str, $offset, $length);
	}
}

//From includes/utf/utf_tools.php
function mbstring_utf8_strlen($text)
{
	return mb_strlen($text, 'utf-8');
}

//From includes/utf/utf_tools.php
function utf8_substr($str, $offset, $length = NULL)
{
	// generates E_NOTICE
	// for PHP4 objects, but not PHP5 objects
	$str = (string) $str;
	$offset = (int) $offset;
	if (!is_null($length))
	{
		$length = (int) $length;
	}

	// handle trivial cases
	if ($length === 0 || ($offset < 0 && $length < 0 && $length < $offset))
	{
		return '';
	}

	// normalise negative offsets (we could use a tail
	// anchored pattern, but they are horribly slow!)
	if ($offset < 0)
	{
		// see notes
		$strlen = utf8_strlen($str);
		$offset = $strlen + $offset;
		if ($offset < 0)
		{
			$offset = 0;
		}
	}

	$op = '';
	$lp = '';

	// establish a pattern for offset, a
	// non-captured group equal in length to offset
	if ($offset > 0)
	{
		$ox = (int) ($offset / 65535);
		$oy = $offset % 65535;

		if ($ox)
		{
			$op = '(?:.{65535}){' . $ox . '}';
		}

		$op = '^(?:' . $op . '.{' . $oy . '})';
	}
	else
	{
		// offset == 0; just anchor the pattern
		$op = '^';
	}

	// establish a pattern for length
	if (is_null($length))
	{
		// the rest of the string
		$lp = '(.*)$';
	}
	else
	{
		if (!isset($strlen))
		{
			// see notes
			$strlen = utf8_strlen($str);
		}

		// another trivial case
		if ($offset > $strlen)
		{
			return '';
		}

		if ($length > 0)
		{
			// reduce any length that would
			// go passed the end of the string
			$length = min($strlen - $offset, $length);

			$lx = (int) ($length / 65535);
			$ly = $length % 65535;

			// negative length requires a captured group
			// of length characters
			if ($lx)
			{
				$lp = '(?:.{65535}){' . $lx . '}';
			}
			$lp = '(' . $lp . '.{'. $ly . '})';
		}
		else if ($length < 0)
		{
			if ($length < ($offset - $strlen))
			{
				return '';
			}

			$lx = (int)((-$length) / 65535);
			$ly = (-$length) % 65535;

			// negative length requires ... capture everything
			// except a group of  -length characters
			// anchored at the tail-end of the string
			if ($lx)
			{
				$lp = '(?:.{65535}){' . $lx . '}';
			}
			$lp = '(.*)(?:' . $lp . '.{' . $ly . '})$';
		}
	}

	if (!preg_match('#' . $op . $lp . '#us', $str, $match))
	{
		return '';
	}

	return $match[1];
}
?>

